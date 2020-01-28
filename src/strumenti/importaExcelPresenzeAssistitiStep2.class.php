<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'presenzaAssistito.class.php';
require_once 'assistito.class.php';
require_once 'strumenti.business.interface.php';
require_once 'importaExcelPresenzeAssistitiStep1.template.php';
require_once 'importaExcelPresenzeAssistitiStep1.class.php';
require_once 'excel_reader2.php';

class ImportaExcelPresenzeAssistitiStep2 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (parent::getIndexSession(self::IMPORTA_PRESENZE_ASSISTITI_STEP2) === NULL) {
            parent::setIndexSession(self::IMPORTA_PRESENZE_ASSISTITI_STEP2, serialize(new ImportaExcelPresenzeAssistitiStep2()));
        }
        return unserialize(parent::getIndexSession(self::IMPORTA_PRESENZE_ASSISTITI_STEP2));
    }
    
    public function go() {

        parent::unsetIndexSessione("messaggioImportFileErr");
        parent::unsetIndexSessione("messaggioImportFileOk");

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();
        $assistito = Assistito::getInstance();
        $presenzaAssistito = PresenzaAssistito::getInstance();

        if (parent::isNotEmpty($presenzaAssistito->getPresenzeTrovate())) {

            $db->beginTransaction();

            $negozio = $presenzaAssistito->getCodneg();
            $presenzeInserite = 0;
            $presenzeIgnorate = 0;

           foreach ($presenzaAssistito->getPresenzeTrovate() as $presenze_row) {

                $numeroCella = 1;
                $datPresenza = "";
                $idAssistito = "";

                foreach ($presenze_row as $presenza_col) {

                    switch ($numeroCella) {
                        case 1:     // nome dell'assistito
                            $assistito->setDesAssistito($presenza_col);
                            $assistito->getIdAssistitoFromName($db);      
                            if (parent::isEmpty($assistito->getIdAssistito())) {
                                if (!$assistito->inserisci($db)) {
                                    // se non l'ho trovato lo creo
                                    parent::setIndexSession("messaggioImportFileErr", "Errore inserimento nuovo ASSISTITO : " . $assistito->getDesAssistito());                                    
                                }
                            }
                            break;
                        case 32:    // è la cella del totale che ignoro
                            break;
                        default:    // la singola presenza

                            if ($presenza_col === "P") {
                                $datPresenza = $presenzaAssistito->getAnno() . "-" . ($presenzaAssistito->getMese() + 1) . "-" . ($numeroCella - 1);
                                $presenzaAssistito->setDatPresenza($datPresenza);
                                $presenzaAssistito->setIdAssistito($assistito->getIdAssistito());
                                
                                if ($presenzaAssistito->isNew($db)) {
                                    if (!$presenzaAssistito->inserisci($db)) {
                                        parent::setIndexSession("messaggioImportFileErr", "Errore inserimento nuova PRESENZA_ASSISTITO : " . $assistito->getDesAssistito() . " - " . $presenzaAssistito->getDatPresenza());                                    
                                    } else {
                                        $presenzeInserite ++;                                
                                    }
                                } else {
                                    $presenzeIgnorate ++;
                                }                            
                            }
                            break;
                    }
                    if (parent::isNotEmpty(parent::getIndexSession("messaggioImportFileErr"))) {
                        break;
                    } else {
                        $numeroCella ++;
                    }
                }
                if (parent::isNotEmpty(parent::getIndexSession("messaggioImportFileErr"))) {
                    break;
                }
            }
            if (parent::isEmpty(parent::getIndexSession("messaggioImportFileErr"))) {
                $db->commitTransaction();
                parent::setIndexSession("buttonDisabled", "disabled");
                parent::setIndexSession(self::AZIONE, parent::EMPTYSTRING);
                parent::setIndexSession("messaggioImportFileOk", "<br/>&ndash; presenze inserite " . $presenzeInserite . "<br/>&ndash; presenze gi&agrave; esistenti " . $presenzeIgnorate);
            }
        }

        $importaExcelPresenzeAssistitoStep1 = ImportaExcelPresenzeAssistitiStep1::getInstance();
        $importaExcelPresenzeAssistitoStep1->start();
    }
    
    public function start() {
    }
}