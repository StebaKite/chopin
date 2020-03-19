<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'presenzaAssistito.class.php';
require_once 'assistito.class.php';
require_once 'strumenti.business.interface.php';
require_once 'importaExcelPresenzeAssistitiStep1.template.php';
require_once 'excel_reader2.php';

class ImportaExcelPresenzeAssistitiStep1 extends StrumentiAbstract implements StrumentiBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::IMPORTA_PRESENZE_ASSISTITI_STEP1) === NULL) {
            parent::setIndexSession(self::IMPORTA_PRESENZE_ASSISTITI_STEP1, serialize(new ImportaExcelPresenzeAssistitiStep1()));
        }
        return unserialize(parent::getIndexSession(self::IMPORTA_PRESENZE_ASSISTITI_STEP1));
    }
    
    public function start() {

        $utility = Utility::getInstance();
        $presenzaAssistito = PresenzaAssistito::getInstance();
        $importaExcelPresenzeAssistitoTemplate = ImportaExcelPresenzeAssistitoStep1Template::getInstance();

        
        if (parent::isEmpty(parent::getIndexSession("buttonDisabled"))) {
            $this->preparaPaginaStep1();
        }

        // Data del giorno preimpostata solo in entrata -------------------------

        if (parent::isEmpty($presenzaAssistito->getAnno())) {
            $presenzaAssistito->setAnno(date("Y"));
        }
        if (parent::isEmpty($presenzaAssistito->getMese())) {
            $presenzaAssistito->setMese(date("m") - 1); // il mese è dimimuito di 1 per coincidere con i fogli del file excel
        }
        if (parent::isEmpty($presenzaAssistito->getCodNeg())) {
            $presenzaAssistito->setCodNeg("VIL");
        }
        IF (parent::isEmpty($presenzaAssistito->getFile())) {
            $presenzaAssistito->setFile(null);
        }
        $presenzaAssistito->setPresenzeTrovate(null);
        $presenzaAssistito->setPresenzeIncomplete(null);
        $presenzaAssistito->setStatoStep1("active");
        $presenzaAssistito->setStatoStep2("disabled");
        
        if (parent::isNotEmpty(parent::getIndexSession("messaggioImportFileOk"))) {
            $presenzaAssistito->setStatoStep1("complete");
            $presenzaAssistito->setStatoStep2("complete");
            $presenzaAssistito->setStatoStep3("complete");            
        }
        else {
            $presenzaAssistito->setStatoStep3("disabled");            
        }

        parent::setIndexSession(self::PRESENZA_ASSISTITO, serialize($presenzaAssistito));

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);
        
        $importaExcelPresenzeAssistitoTemplate->displayPagina();
        include($this->piede);

        if (parent::isNotEmpty(parent::getIndexSession("messaggioImportFileOk"))) {
            $replace = array('%messaggio%' => parent::getIndexSession("messaggioImportFileOk"));
            parent::unsetIndexSessione("messaggioImportFileOk");
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), $replace);
            echo $utility->tailTemplate($template);
        } else {
            if (parent::isNotEmpty(parent::getIndexSession("messaggioImportFileErr"))) {
                $replace = array('%messaggio%' => parent::getIndexSession("messaggioImportFileErr"));
                parent::unsetIndexSessione("messaggioImportFileErr");
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), $replace);
                echo $utility->tailTemplate($template);
            }
        }
    }
    
    public function go() {
        
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $presenzaAssistito = PresenzaAssistito::getInstance();
        
        parent::unsetIndexSessione("messaggioImportFileErr");
        parent::unsetIndexSessione("messaggioImportFileOk");

        $importaExcelPresenzeAssistitoTemplate = ImportaExcelPresenzeAssistitoStep1Template::getInstance();

        if ($importaExcelPresenzeAssistitoTemplate->controlliLogici()) {

            $users = shell_exec("who | cut -d' ' -f1 | sort | uniq");

            // Prelievo filepath in base al sistema operativo ospitante
            
            $agent = parent::getInfoFromServer('HTTP_USER_AGENT');
            if (strpos($agent, 'Windows') === false) {
                $filepath = $array["linuxFilePath"];
            } else {
                $filepath = $array["windowsFilePath"];                
            }
            
            // sostituzione della userid all'interno del filepath prelevato in configurazione
            
            if (strpos($users, $array['usernameProdLogin']) === false) {
                $path = str_replace("%user%", "stefano", $filepath);
            } else {
                $path = str_replace("%user%", $array["usernameProdLogin"], $filepath);
            }

            if (strpos($agent, 'Windows') === false) {
                $data = new Spreadsheet_Excel_Reader($path . "/" . $presenzaAssistito->getFile());
            } else {
                $data = new Spreadsheet_Excel_Reader($path . "\\" . $presenzaAssistito->getFile());
            }
            
            $sheets = $presenzaAssistito->getMese();
            $mese = str_pad($presenzaAssistito->getMese() + 1, 2, "0", STR_PAD_LEFT);
            
            $presenze_sheet = array();
            $complete = 0;
            $incomplete = 0;

            // Ciclo righe
            for ($i = 5; $i <= $data->sheets[$sheets]['numRows']; $i++) {
                
                $presenza_sheet = array();

                // Ciclo colonne
                for ($j = 1; $j <= $data->sheets[$sheets]['numCols']; $j++) {
                    if ($j >= 2) {
                        if (isset($data->sheets[$sheets]['cells'][$i][$j])) {
                            if ($data->sheets[$sheets]['cells'][$i][$j] != "") {
                                $cella = $data->sheets[$sheets]['cells'][$i][$j];
                                if ($j === 2) {
                                    array_push($presenza_sheet, $cella);
                                } else {
                                    if (is_numeric($cella)) {
                                        $cella = $data->sheets[$sheets]['cells'][$i][$j];
                                        if ($j <= 31) {
                                            if ($cella === "1") {
                                                $cella = "P";
                                            } else {
                                                if ($cella === "0") {
                                                    $cella = parent::EMPTYSTRING;
                                                }
                                            }                                                
                                        }
                                        array_push($presenza_sheet, $cella);                                            
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($presenza_sheet) > 1) {
                    array_push($presenze_sheet, $presenza_sheet);
                    unset($presenza_sheet);
                    $complete ++;
                } else {
                    if (count($presenza_sheet) === 1) {
                        $incomplete ++;
                        error_log("INCOMPLETE => " . $presenza_sheet);
                    }
                }
            }
                
            $presenzaAssistito->setPresenzeTrovate($presenze_sheet);
            $presenzaAssistito->setPresenzeIncomplete($incomplete);

            
            
            
            if (($incomplete === 0) and ( $complete > 0)) {
                $presenzaAssistito->setStatoStep1("complete");
                $presenzaAssistito->setStatoStep2("active");                
                $presenzaAssistito->setStatoStep3("disabled");
                parent::unsetIndexSessione("messaggioImportFileErr");
                parent::setIndexSession("buttonDisabled", parent::EMPTYSTRING);
                parent::setIndexSession("messaggioImportFileOk", "Tabella presenze di " . $presenzaAssistito->getNomeMese() . " corretta, puoi procedere con l'importazione");
                $this->preparaPaginaStep2($importaExcelPresenzeAssistitoTemplate);
            } else {
                
                if (($incomplete === 0) and ( $complete === 0)) {
                    $presenzaAssistito->setStatoStep1("active");
                    $presenzaAssistito->setStatoStep2("disabled");
                    $presenzaAssistito->setStatoStep3("disabled");
                    parent::setIndexSession("messaggioImportFileErr", "La tabella delle presenze di " . $presenzaAssistito->getNomeMese() . " non ha un formato corretto");
                    $this->preparaPaginaStep1();
                } else {
                    $presenzaAssistito->setStatoStep1("active");
                    $presenzaAssistito->setStatoStep2("disabled");
                    $presenzaAssistito->setStatoStep3("disabled");
                    parent::setIndexSession("messaggioImportFileErr", "La tabella delle presenze di " . $presenzaAssistito->getNomeMese() . " non ha un formato corretto");
                    parent::setIndexSession("buttonDisabled", "disabled");
                    $this->preparaPaginaStep1();
                }
            }
            
            parent::setIndexSession(self::PRESENZA_ASSISTITO, serialize($presenzaAssistito));

            // Compone la pagina
            $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
            $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
            echo $utility->tailTemplate($template);
        }
        else {

            $this->preparaPaginaStep1();

            // Compone la pagina
            $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
            $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
            echo $utility->tailTemplate($template);

            $replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), $replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        }
        $importaExcelPresenzeAssistitoTemplate->displayPagina();
        include($this->piede);        
    }

    public function preparaPaginaStep1() {
        
        parent::setIndexSession(self::AZIONE, self::AZIONE_IMPORTA_PRESENZE_ASSISTITI_STEP1);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.confermaimportaExcelPresenzeAssistitiStep1%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.importaExcelPresenzeAssistitiStep1%");
    }

    public function preparaPaginaStep2() {
        
        parent::setIndexSession(self::AZIONE, self::AZIONE_IMPORTA_PRESENZE_ASSISTITI_STEP2);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.confermaimportaExcelPresenzeAssistitiStep2%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.importaExcelPresenzeAssistitiStep2%");
    }

}