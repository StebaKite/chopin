<?php

require_once 'riepiloghiComparati.abstract.class.php';
require_once 'riepiloghi.extractor.interface.php';
require_once 'utility.class.php';
require_once 'pdf.class.php';
require_once 'riepilogo.class.php';

class EstraiPdfRiepilogoNegozio extends RiepiloghiComparatiAbstract implements RiepiloghiBusinessInterface {

    public $_datiMCT = array();

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public function getInstance() {

        if (!isset($_SESSION[self::ESTRAI_PDF_RIEPILOGO_NEGOZIO]))
            $_SESSION[self::ESTRAI_PDF_RIEPILOGO_NEGOZIO] = serialize(new EstraiPdfRiepilogoNegozio());
        return unserialize($_SESSION[self::ESTRAI_PDF_RIEPILOGO_NEGOZIO]);
    }

    public function start() {

        $riepilogo = Riepilogo::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $pdf = Pdf::getInstance();
        $pdf->setLogo($this->root . $array["logo"]);
        $pdf->setCreator($array["productName"]);


        $pdf->AliasNbPages();

        /**
         * Generazione del documento
         */
        $pdf = $this->generaSezioneIntestazione($pdf, $riepilogo);
        $pdf = $this->generaSezioneTabellaCosti($pdf, $riepilogo);
        $pdf = $this->generaSezioneTabellaRicavi($pdf, $riepilogo);
        $pdf = $this->generaSezioneTabellaTotali($pdf, $riepilogo);

        if ($riepilogo->getSoloContoEconomico() == "N") {
            $pdf = $this->generaSezioneTabellaAttivo($pdf, $riepilogo);
            $pdf = $this->generaSezioneTabellaPassivo($pdf, $riepilogo);
        }

        $pdf = $this->generaSezioneTabellaMct($pdf, $riepilogo);
        $pdf = $this->generaSezioneTabellaBep($pdf, $riepilogo);

        $pdf->Output();
    }

    public function go() {

    }

    public function generaSezioneIntestazione($pdf, $riepilogo) {

        $pdf->setTitle("Riepilogo Costi e Ricavi Negozi");
        $pdf->setTitle1("Dal " . $riepilogo->getDataregDa() . " al " . $riepilogo->getDataregA());

        return $pdf;
    }

    private function generaSezioneTabellaCosti($pdf, $riepilogo) {

        $pdf->AddPage('L');

        $header = array("Costi", self::NEGOZIO_BREMBATE, self::NEGOZIO_TREZZO, self::NEGOZIO_VILLA, "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziTable($header, $riepilogo->getCostiComparati(), 1);

        return $pdf;
    }

    private function generaSezioneTabellaRicavi($pdf, $riepilogo) {

        $pdf->Cell(100, 10, '', '', 0, 'R', $fill);
        $pdf->Ln();

        $header = array("Ricavi", self::NEGOZIO_BREMBATE, self::NEGOZIO_TREZZO, self::NEGOZIO_VILLA, "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziTable($header, $riepilogo->getRicaviComparati(), -1);

        return $pdf;
    }

    private function generaSezioneTabellaTotali($pdf, $riepilogo) {

        $nomeTabella = strtoupper($this->nomeTabTotali($riepilogo->getTotaleRicavi(), $riepilogo->getTotaleCosti()));

        /**
         * Calcolo dell'utile per negozio
         */
        $riepilogo->setUtileBrembate($riepilogo->getTotaleRicaviBrembate() - $riepilogo->getTotaleCostiBrembate());
        $riepilogo->setUtileTrezzo($riepilogo->getTotaleRicaviTrezzo() - $riepilogo->getTotaleCostiTrezzo());
        $riepilogo->setUtileVilla($riepilogo->getTotaleRicaviVilla() - $riepilogo->getTotaleCostiVilla());
        $riepilogo->setTotaleUtile($riepilogo->getUtileBrembate() + $riepilogo->getUtileTrezzo() + $riepilogo->getUtileVilla());

        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);

        $pdf->AddPage('L');

        $header = array($nomeTabella, parent::NEGOZIO_BREMBATE, parent::NEGOZIO_TREZZO, parent::NEGOZIO_VILLA, "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziTotaliTable($header, $riepilogo);

        return $pdf;
    }

    private function generaSezioneTabellaAttivo($pdf, $riepilogo) {

        $pdf->AddPage('L');

        $header = array("Attivo", parent::NEGOZIO_BREMBATE, parent::NEGOZIO_TREZZO, parent::NEGOZIO_VILLA, "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziTable($header, $riepilogo->getAttivoComparati(), 1);

        return $pdf;
    }

    private function generaSezioneTabellaPassivo($pdf, $riepilogo) {

        $pdf->AddPage('L');

        $header = array("Passivo", parent::NEGOZIO_BREMBATE, parent::NEGOZIO_TREZZO, parent::NEGOZIO_VILLA, "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziTable($header, $riepilogo->getPassivoComparati(), 1);

        return $pdf;
    }

    private function generaSezioneTabellaMct($pdf, $riepilogo) {

//        $this->ricercaCostiVariabiliNegozi($utility, $db);
//        $this->ricercaCostiFissiNegozi($utility, $db);
//        $this->ricercaRicaviFissiNegozi($utility, $db);
        // Villa ---------------------------------------------------------------------

        foreach ($riepilogo->getCostoVariabileVilla() as $row) {
            $datiMCT["totaleCostiVariabiliVIL"] = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiVilla() as $row) {
            $datiMCT["totaleRicaviVIL"] = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoVilla() as $row) {
            $datiMCT["totaleCostiFissiVIL"] = trim($row['totalecostofisso']);
        }

        $datiMCT["margineTotaleVIL"] = abs($datiMCT["totaleRicaviVIL"]) - $datiMCT["totaleCostiVariabiliVIL"];
        $datiMCT["marginePercentualeVIL"] = ($datiMCT["margineTotaleVIL"] * 100 ) / abs($datiMCT["totaleRicaviVIL"]);
        $datiMCT["ricaricoPercentualeVIL"] = ($datiMCT["margineTotaleVIL"] * 100 ) / abs($datiMCT["totaleCostiVariabiliVIL"]);

        $datiMCT["totaleCostiFissi"] += $datiMCT["totaleCostiFissiVIL"];
        $datiMCT["totaleRicavi"] += $datiMCT["totaleRicaviVIL"];
        $datiMCT["totaleCostiVariabili"] += $datiMCT["totaleCostiVariabiliVIL"];

        // Trezzo ---------------------------------------------------------------------

        foreach ($riepilogo->getCostoVariabileTrezzo() as $row) {
            $datiMCT["totaleCostiVariabiliTRE"] = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiTrezzo() as $row) {
            $datiMCT["totaleRicaviTRE"] = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoTrezzo() as $row) {
            $datiMCT["totaleCostiFissiTRE"] = trim($row['totalecostofisso']);
        }

        $datiMCT["margineTotaleTRE"] = abs($datiMCT["totaleRicaviTRE"]) - $datiMCT["totaleCostiVariabiliTRE"];
        $datiMCT["marginePercentualeTRE"] = ($datiMCT["margineTotaleTRE"] * 100 ) / abs($datiMCT["totaleRicaviTRE"]);
        $datiMCT["ricaricoPercentualeTRE"] = ($datiMCT["margineTotaleTRE"] * 100 ) / abs($datiMCT["totaleCostiVariabiliTRE"]);

        $datiMCT["totaleCostiFissi"] += $datiMCT["totaleCostiFissiTRE"];
        $datiMCT["totaleRicavi"] += $datiMCT["totaleRicaviTRE"];
        $datiMCT["totaleCostiVariabili"] += $datiMCT["totaleCostiVariabiliTRE"];

        // Brembate ---------------------------------------------------------------------

        foreach ($riepilogo->getCostoVariabileBrembate() as $row) {
            $datiMCT["totaleCostiVariabiliBRE"] = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiBrembate() as $row) {
            $datiMCT["totaleRicaviBRE"] = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoBrembate() as $row) {
            $datiMCT["totaleCostiFissiBRE"] = trim($row['totalecostofisso']);
        }

        $datiMCT["margineTotaleBRE"] = abs($datiMCT["totaleRicaviBRE"]) - $datiMCT["totaleCostiVariabiliBRE"];
        $datiMCT["marginePercentualeBRE"] = ($datiMCT["margineTotaleBRE"] * 100 ) / abs($datiMCT["totaleRicaviBRE"]);
        $datiMCT["ricaricoPercentualeBRE"] = ($datiMCT["margineTotaleBRE"] * 100 ) / abs($datiMCT["totaleCostiVariabiliBRE"]);

        $datiMCT["totaleCostiFissi"] += $datiMCT["totaleCostiFissiBRE"];
        $datiMCT["totaleRicavi"] += $datiMCT["totaleRicaviBRE"];
        $datiMCT["totaleCostiVariabili"] += $datiMCT["totaleCostiVariabiliBRE"];

        // MCT toale negozi ----------------------------------------------------------

        $datiMCT["margineTotale"] = abs($datiMCT["totaleRicavi"]) - $datiMCT["totaleCostiVariabili"];
        $datiMCT["marginePercentuale"] = ($datiMCT["margineTotale"] * 100 ) / abs($datiMCT["totaleRicavi"]);
        $datiMCT["ricaricoPercentuale"] = ($datiMCT["margineTotale"] * 100 ) / abs($datiMCT["totaleCostiVariabili"]);

        $riepilogo->setDatiMCT($datiMCT);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);

        // Nuova pagina documento -----------------------------------------------

        $pdf->AddPage('L');

        $header = array("MCT", parent::NEGOZIO_BREMBATE, parent::NEGOZIO_TREZZO, parent::NEGOZIO_VILLA, "TOTALE");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziMctTable($header, $riepilogo->getDatiMCT());

        return $pdf;
    }

    /**
     * Questo metodo calcola il BEP per tutti i negozi.
     * Utilizza gli stessi dati estratti per il calcolo del margine di contribuzione (MCT) e aggiunge all'array i totali calcolati
     * @param object $pdf
     * @param object $riepilogo
     */
    private function generaSezioneTabellaBep($pdf, $riepilogo) {

        $datiMCT = $riepilogo->getDatiMCT();

        // Villa ---------------------------------------------------------------------

        $datiMCT["incidenzaCostiVariabiliSulFatturatoVIL"] = 1 - ($datiMCT["totaleCostiVariabiliVIL"] / abs($datiMCT["totaleRicaviVIL"]));
        $datiMCT["bepVIL"] = $datiMCT["totaleCostiFissiVIL"] / round($datiMCT["incidenzaCostiVariabiliSulFatturatoVIL"], 2);

        // Trezzo ---------------------------------------------------------------------

        $datiMCT["incidenzaCostiVariabiliSulFatturatoTRE"] = 1 - ($datiMCT["totaleCostiVariabiliTRE"] / abs($datiMCT["totaleRicaviTRE"]));
        $datiMCT["bepTRE"] = $datiMCT["totaleCostiFissiTRE"] / round($datiMCT["incidenzaCostiVariabiliSulFatturatoTRE"], 2);

        // Brembate ---------------------------------------------------------------------

        $datiMCT["incidenzaCostiVariabiliSulFatturatoBRE"] = 1 - ($datiMCT["totaleCostiVariabiliBRE"] / abs($datiMCT["totaleRicaviBRE"]));
        $datiMCT["bepBRE"] = $datiMCT["totaleCostiFissiBRE"] / round($datiMCT["incidenzaCostiVariabiliSulFatturatoBRE"], 2);

        // BEP totale negozi -----------------------------------------------------------

        $datiMCT["incidenzaCostiVariabiliSulFatturato"] = 1 - ($datiMCT["totaleCostiVariabili"] / abs($datiMCT["totaleRicavi"]));
        $datiMCT["bep"] = $datiMCT["totaleCostiFissi"] / round($datiMCT["incidenzaCostiVariabiliSulFatturato"], 2);


        $riepilogo->setDatiMCT($datiMCT);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);

        // Nuova pagina documento -----------------------------------------------

        $pdf->Cell(100, 10, '', '', 0, 'R', $fill);
        $pdf->Ln();

        $header = array("BEP", parent::NEGOZIO_BREMBATE, parent::NEGOZIO_TREZZO, parent::NEGOZIO_VILLA, "TOTALE");
        $pdf->SetFont('Arial', '', 9);
        $pdf->riepilogoNegoziBepTable($header, $riepilogo->getDatiMCT());

        return $pdf;
    }

}

?>
