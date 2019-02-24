<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'corrispettivo.class.php';
require_once 'strumenti.business.interface.php';
require_once 'importaExcelCorrispettiviNegozioStep1.template.php';
require_once 'excel_reader2.php';

class ImportaExcelCorrispettiviNegozioStep1 extends StrumentiAbstract implements StrumentiBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1]))
            $_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1] = serialize(new ImportaExcelCorrispettiviNegozioStep1());
        return unserialize($_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1]);
    }

    public function start() {

        $utility = Utility::getInstance();
        $corrispettivo = Corrispettivo::getInstance();
        $importaExcelCorrispettiviNegozioTemplate = ImportaExcelCorrispettiviNegozioStep1Template::getInstance();

        $this->preparaPaginaStep1($importaExcelCorrispettiviNegozioTemplate);

        // Data del giorno preimpostata solo in entrata -------------------------

        $corrispettivo->setAnno(date("Y"));
        $corrispettivo->setMese(date("m") - 1); // il mese è dimimuito di 1 per coincidere con i fogli del file excel
        $corrispettivo->setCodNeg("VIL");
        $corrispettivo->setContoCassa("S");

        if (parent::isEmpty($corrispettivo->getDatada()))
            $corrispettivo->setDatada(date("01/m/Y"));
        
        if (parent::isEmpty($corrispettivo->getDataa()))
            $corrispettivo->setDataa(date("d/m/Y"));

        $corrispettivo->setFile(null);
        $corrispettivo->setCorrispettiviTrovati(null);
        $corrispettivo->setCorrispettiviIncompleti(null);
        $corrispettivo->setStatoStep1("active");
        $corrispettivo->setStatoStep2("disabled");
        
        if (isset($_SESSION["messaggioImportFileOk"])) {
            $corrispettivo->setStatoStep1("complete");
            $corrispettivo->setStatoStep2("complete");
            $corrispettivo->setStatoStep3("complete");            
        }
        else {$corrispettivo->setStatoStep3("disabled");};

        $_SESSION[self::CORRISPETTIVO] = serialize($corrispettivo);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);
        
        $importaExcelCorrispettiviNegozioTemplate->displayPagina();
        include($this->piede);

        if (isset($_SESSION["messaggioImportFileOk"])) {
            self::$replace = array('%messaggio%' => $_SESSION["messaggioImportFileOk"]);
            unset($_SESSION["messaggioImportFileOk"]);
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
            echo $utility->tailTemplate($template);
        } else {
            if (isset($_SESSION["messaggioImportFileErr"])) {
                self::$replace = array('%messaggio%' => $_SESSION["messaggioImportFileErr"]);
                unset($_SESSION["messaggioImportFileErr"]);
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
                echo $utility->tailTemplate($template);
            }
        }
    }

    public function go() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $corrispettivo = Corrispettivo::getInstance();

        $importaExcelCorrispettiviNegozioTemplate = ImportaExcelCorrispettiviNegozioStep1Template::getInstance();

        if ($importaExcelCorrispettiviNegozioTemplate->controlliLogici()) {

            $users = shell_exec("who | cut -d' ' -f1 | sort | uniq");

            if (strpos($users, $array['usernameProdLogin']) === false) {
                $path = str_replace("%user%", "stefano", $array["filePath"]);
            } else {
                $path = str_replace("%user%", $array["usernameProdLogin"], $array["filePath"]);
            }

            $data = new Spreadsheet_Excel_Reader($path . "/" . $corrispettivo->getFile());
            $sheets = $corrispettivo->getMese();
            $mese = str_pad($corrispettivo->getMese() + 1, 2, "0", STR_PAD_LEFT);

            $dataDa = strtotime(str_replace("/", "-", $corrispettivo->getDatada()));
            $dataA = strtotime(str_replace("/", "-", $corrispettivo->getDataa()));

            $corrispettivi_sheet = array();
            $completi = 0;
            $incompleti = 0;

            // Ciclo righe
            for ($i = 1; $i <= $data->sheets[$sheets]['numRows']; $i++) {

                $corrispettivo_sheet = array();

                // Ciclo colonne
                for ($j = 1; $j <= $data->sheets[$sheets]['numCols']; $j++) {
                    if ($j <= 4) {
                        if ($data->sheets[$sheets]['cells'][$i][$j] != "") {
                            if (is_numeric($data->sheets[$sheets]['cells'][$i][$j])) {

                                $cella = $data->sheets[$sheets]['cells'][$i][$j];
                                if ($j == 1) {
                                    $giorno = str_pad($data->sheets[$sheets]['cells'][$i][$j], 2, "0", STR_PAD_LEFT);
                                    $cella = $giorno . "/" . $mese . "/" . $corrispettivo->getAnno();
                                    $datareg = strtotime(str_replace("/", "-", $cella));
                                }
                                if (($datareg >= $dataDa) and ( $datareg <= $dataA)) {
                                    array_push($corrispettivo_sheet, $cella);
                                }
                            }
                        }
                    }
                }
                if (count($corrispettivo_sheet) == 4) {
                    array_push($corrispettivi_sheet, $corrispettivo_sheet);
                    unset($corrispettivo_sheet);
                    $completi ++;
                } else {
                    if (count($corrispettivo_sheet) > 0) {
                        $incompleti ++;
                        error_log("INCOMPLETO => " . $corrispettivo_sheet);
                    }
                }
            }

            $corrispettivo->setCorrispettiviTrovati($corrispettivi_sheet);
            $corrispettivo->setCorrispettiviIncompleti($incompleti);
            
            if (($incompleti == 0) and ( $completi > 0)) {
                $corrispettivo->setStatoStep1("complete");
                $corrispettivo->setStatoStep2("active");                
                $corrispettivo->setStatoStep3("disabled");                
                $this->preparaPaginaStep2($importaExcelCorrispettiviNegozioTemplate);
            } else {
                $corrispettivo->setStatoStep1("active");
                $corrispettivo->setStatoStep2("disabled");
                $corrispettivo->setStatoStep3("disabled");
                $this->preparaPaginaStep1($importaExcelCorrispettiviNegozioTemplate);
            }

            $_SESSION[self::CORRISPETTIVO] = serialize($corrispettivo);

            // Compone la pagina
            $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
            $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
            echo $utility->tailTemplate($template);
        }
        else {

            $this->preparaPaginaStep1($importaExcelCorrispettiviNegozioTemplate);

            // Compone la pagina
            $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
            $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
            echo $utility->tailTemplate($template);
            
            self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        $importaExcelCorrispettiviNegozioTemplate->displayPagina();
        include($this->piede);        
    }

    public function preparaPaginaStep1($importaExcelCorrispettiviNegozioTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1;
        $_SESSION[self::TIP_CONFERMA] = "%ml.confermaimportaExcelCorrispettivoNegozioStep1%";
        $_SESSION[self::TITOLO_PAGINA] = "%ml.importaExcelCorrispettivoNegozioStep1%";
    }

    public function preparaPaginaStep2($importaExcelCorrispettiviNegozioTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_IMPORTA_CORRISPETTIVI_NEGOZIO_STEP2;
        $_SESSION[self::TIP_CONFERMA] = "%ml.confermaimportaExcelCorrispettivoNegozioStep2%";
        $_SESSION[self::TITOLO_PAGINA] = "%ml.importaExcelCorrispettivoNegozioStep2%";
    }

}
