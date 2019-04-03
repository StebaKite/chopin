<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'corrispettivo.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'strumenti.business.interface.php';
require_once 'importaExcelCorrispettiviNegozioStep1.template.php';
require_once 'importaExcelCorrispettiviNegozioStep1.class.php';
require_once 'excel_reader2.php';

class ImportaExcelCorrispettiviNegozioStep2 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (!isset($_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP2]))
            $_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP2] = serialize(new ImportaExcelCorrispettiviNegozioStep2());
        return unserialize($_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP2]);
    }

    public function go() {

        unset($_SESSION["messaggioImportFileErr"]);
        unset($_SESSION["messaggioImportFileOk"]);

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();
        $corrispettivo = Corrispettivo::getInstance();
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();

        if (parent::isNotEmpty($corrispettivo->getCorrispettiviTrovati())) {

            $db->beginTransaction();

            $negozio = $corrispettivo->getCodneg();
            $causale = $array["corrispettiviNegozio"];
            $stareg = "00";
            $corrispettiviInseriti = 0;
            $corrispettiviIgnorati = 0;

            foreach ($corrispettivo->getCorrispettiviTrovati() as $corrispettivo_row) {

                $numeroCella = 1;
                $datareg = "";
                $totale = "";
                $importo10 = "";
                $importo22 = "";
                $iva10 = "";
                $iva22 = "";
                $contoCorrispettivo = explode(" - ", $array['contoCorrispettivoNegozi']);

                foreach ($corrispettivo_row as $corrispettivo_col) {

                    /**
                     * Prelevo tutte le celle della riga
                     */
                    switch ($numeroCella) {
                        case 1:
                            $datareg = $corrispettivo_col;
                            break;
                        case 2:
                            $totale = $corrispettivo_col;
                            break;
                        case 3:
                            $importo10 = $corrispettivo_col;
                            $iva10 = 1.10;
                            break;
                        case 4:
                            $importo22 = $corrispettivo_col;
                            $iva22 = 1.22;
                            break;
                    }
                    $numeroCella ++;
                }

                /**
                 * Controllo gli importi dei due reparti per vedere se creare il corrispettivo
                 */
                if ($importo10 > 0) {

                    /**
                     * Controllo che il corrispettivo non sia già stato inserito
                     */
                    if ($this->isNew($db, $utility, $datareg, $negozio, $contoCorrispettivo[0], $importo10)) {
                        $corrispettiviInseriti ++;
                        $dettagliInseriti = $this->generaDettagliCorrispettivo($array, $importo10, $iva10, $corrispettivo);
                        if (!$this->creaCorrispettivoNegozio($db, $utility, $registrazione, $dettaglioRegistrazione ,$corrispettivo->getCodneg(), $datareg, $causale, $stareg, $dettagliInseriti)) {
                            $_SESSION["messaggioImportFileErr"] = "Errore imprevisto, ripristino eseguito";
                            break;
                        }
                    } else
                        $corrispettiviIgnorati ++;
                }

                if ($importo22 > 0) {

                    if ($this->isNew($db, $utility, $datareg, $negozio, $contoCorrispettivo[0], $importo22)) {
                        $corrispettiviInseriti ++;
                        $dettagliInseriti = $this->generaDettagliCorrispettivo($array, $importo22, $iva22, $corrispettivo);
                        if (!$this->creaCorrispettivoNegozio($db, $utility, $registrazione, $dettaglioRegistrazione ,$corrispettivo->getCodneg(), $datareg, $causale, $stareg, $dettagliInseriti)) {
                            $_SESSION["messaggioImportFileErr"] = "Errore imprevisto, ripristino eseguito";
                            break;
                        }
                    } else
                        $corrispettiviIgnorati ++;
                }
            }
            if (!isset($_SESSION["messaggioImportFileErr"])) {
                $db->commitTransaction();
                $_SESSION["messaggioImportFileOk"] = "<br/>&ndash; corrispettivi inseriti " . $corrispettiviInseriti;
                $_SESSION["messaggioImportFileOk"] .= "<br/>&ndash; corrispettivi gi&agrave; esistenti " . $corrispettiviIgnorati;
            }
        }

        $importaExcelCorrispettiviNegozioStep1 = ImportaExcelCorrispettiviNegozioStep1::getInstance();
        $importaExcelCorrispettiviNegozioStep1->start();
    }

    private function generaDettagliCorrispettivo($array, $importo, $aliquota, $corrispettivo) {

        $dettagliInseriti = array();
        $dettaglio = array();

        if ($corrispettivo->getContoCassa() == "S")
            $contoDare = explode(" - ", $array['contoCassa']);
        else
            $contoDare = explode(" - ", $array['contoBanca']);

        $contoErario = explode(" - ", $array['contoErarioNegozi']);
        $contoCorrispettivo = explode(" - ", $array['contoCorrispettivoNegozi']);

        /**
         * Primo dettaglio registrazione
         */
        array_push($dettaglio, $contoDare[0]);
        array_push($dettaglio, $importo);
        array_push($dettaglio, "D");

        array_push($dettagliInseriti, $dettaglio);
        unset($dettaglio);

        /**
         * Secondo dettaglio registrazione
         */
        $dettaglio = array();

        $imponibile = round($importo / $aliquota, 2);
        $iva = round($imponibile * (round($aliquota / 10, 1)), 2);

        // sistemazione della squadratura generata dagli arrotondamenti
        $differenza = round($importo - ($imponibile + $iva), 2);
        if ($differenza < 0)
            $imponibile += $differenza;
        if ($differenza > 0)
            $iva -= $differenza;

        array_push($dettaglio, $contoErario[0]);
        array_push($dettaglio, $iva);
        array_push($dettaglio, "A");

        array_push($dettagliInseriti, $dettaglio);
        unset($dettaglio);

        /**
         * Terzo dettaglio registrazione
         */
        $dettaglio = array();

        array_push($dettaglio, $contoCorrispettivo[0]);
        array_push($dettaglio, $imponibile);
        array_push($dettaglio, "A");

        array_push($dettagliInseriti, $dettaglio);
        unset($dettaglio);

        return $dettagliInseriti;
    }

    private function creaCorrispettivoNegozio($db, $utility, $registrazione, $dettaglioRegistrazione, $codneg, $datareg, $causale, $stareg, $dettagliInseriti) {

        $descreg = "Incasso corrispettivi negozio di " . $codneg;

        $registrazione->setDatRegistrazione($datareg);
        $registrazione->setCodCausale($causale);
        $registrazione->setStaRegistrazione($stareg);
        $registrazione->setCodNegozio($codneg);
        $registrazione->setDesRegistrazione($descreg);
        $registrazione->setDatScadenza(parent::EMPTYSTRING);
        $registrazione->setIdFornitore(parent::EMPTYSTRING);
        $registrazione->setIdCliente(parent::EMPTYSTRING);
        $registrazione->setIdMercato(parent::EMPTYSTRING);
        $registrazione->setNumFattura(parent::EMPTYSTRING);

        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
        
        if ($registrazione->inserisci($db)) {

            foreach ($dettagliInseriti as $dettaglio) {

                $numEle = 1;
                foreach ($dettaglio as $ele) {

                    switch ($numEle) {
                        case 1:
                            $conto = substr($ele, 0, 3);
                            $sottoConto = substr($ele, 3);
                            break;
                        case 2:
                            $importo = $ele;
                            break;
                        case 3:
                            $d_a = $ele;
                            break;
                    }
                    $numEle ++;
                }
                
                $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
                $dettaglioRegistrazione->setCodConto($conto);
                $dettaglioRegistrazione->setCodSottoconto($sottoConto);
                $dettaglioRegistrazione->setImpRegistrazione($importo);
                $dettaglioRegistrazione->setIndDareavere($d_a);
                
                if (!$dettaglioRegistrazione->inserisci($db)) {
                    $db->rollbackTransaction();
                    error_log("Errore inserimento dettaglio registrazione, eseguito Rollback");
                    return FALSE;
                }
            }
            
            /**
             * Ricalcolo i saldi dei conti
             */
            $this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
            $db->commitTransaction();
            return TRUE;
        }
        $db->rollbackTransaction();
        error_log("Errore inserimento registrazione, eseguito Rollback");
        return FALSE;
    }

    public function start() {
        
    }
}