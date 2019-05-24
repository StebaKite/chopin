<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'pdf.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class EsportaMovimentiSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::ESPORTA_MOVIMENTI_SOTTOCONTO]))
            $_SESSION[self::ESPORTA_MOVIMENTI_SOTTOCONTO] = serialize(new EsportaMovimentiSottoconto());
        return unserialize($_SESSION[self::ESPORTA_MOVIMENTI_SOTTOCONTO]);
    }

    public function start() {
        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $logo = $this->root . $array["logo"];
        $creator = "Nexus6";

        $pdf = Pdf::getInstance();

        $pdf->AliasNbPages();
        $pdf->setLogo($logo);
        $pdf->setCreator($creator);

        /**
         * Generazione del documento
         */
        $sottoconto = Sottoconto::getInstance();
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($sottoconto->getCodSottoconto());
        $conto = Conto::getInstance();

        $this->generaSezioneIntestazione($pdf, $conto, $sottoconto);
        $this->generaSezioneTabellaMastrinoConto($pdf, $utility, $sottoconto);

        $pdf->Output("mastrino.pdf", "I");
    }

    public function go() {
        $this->start();
    }

    public function generaSezioneIntestazione($pdf, $conto, $sottoconto) {

        $pdf->setTitle("Registrazioni dal " . $sottoconto->getDataRegistrazioneDa() . " al " . $sottoconto->getDataRegistrazioneA());

        $negozio = "";
        $negozio = ($sottoconto->getCodNegozio() == "VIL") ? "Villa D'Adda" : $negozio;
        $negozio = ($sottoconto->getCodNegozio() == "BRE") ? "Brembate" : $negozio;
        $negozio = ($sottoconto->getCodNegozio() == "TRE") ? "Trezzo" : $negozio;

        if ($negozio != "")
            $pdf->setTitle1("Negozio di " . $negozio);
        else
            $pdf->setTitle1("Tutti i negozi");

        $pdf->setTitle2($conto->getCatConto() . " : " . $conto->getDesConto() . " / " . $sottoconto->getDesSottoconto());
    }

    public function generaSezioneTabellaMastrinoConto($pdf, $utility, $sottoconto) {

        $pdf->AddPage();

        $header = array("Data", "Descrizione", "Dare", "Avere", "Saldo");
        $this->makeMastrinoContoTable($pdf, $header, $sottoconto->getRegistrazioniTrovate());
    }

    public function makeMastrinoContoTable($pdf, $header, $data) {

        // Colors, line width and bold font
        $pdf->SetFillColor(28, 148, 196);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(128, 0, 0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Arial', 'B', 10);

        // Header
        $w = array(20, 80, 30, 30, 30);
        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $pdf->Ln();

        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 10);

        // Data
        $totaleDare = 0;
        $totaleAvere = 0;
        $saldo = 0;

        $fill = false;
        foreach ($data as $row) {

            if ($row['ind_dareavere'] == 'D') {
                $totaleDare = $totaleDare + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $impDare = $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $euroAvere = "";
                $impAvere = "";
            } elseif ($row['ind_dareavere'] == 'A') {
                $totaleAvere = $totaleAvere + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $impDare = "";
                $impAvere = $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $euroDare = "";
            }

            if (trim($row[Conto::TIP_CONTO]) == self::CONTO_IN_DARE) {
                $saldo = $totaleDare - $totaleAvere;
            } elseif (trim($row[Conto::TIP_CONTO]) == self::CONTO_IN_AVERE) {
                $saldo = $totaleAvere - $totaleDare;
            }

            $fill = !$fill;

            $pdf->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', date("d/m/Y", strtotime($row[Registrazione::DAT_REGISTRAZIONE]))), 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 6, iconv('UTF-8', 'windows-1252', trim($row[Registrazione::DES_REGISTRAZIONE])), 'LR', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, number_format($impDare, 2, ',', '.'), 'LR', 0, 'R', $fill);
            $pdf->Cell($w[3], 6, number_format($impAvere, 2, ',', '.'), 'LR', 0, 'R', $fill);

            if ($saldo < 0) {
                $pdf->SetTextColor(255, 0, 0);
                $pdf->SetFontSize(10);
            }

            $pdf->Cell($w[4], 6, number_format($saldo, 2, ',', '.'), 'LR', 0, 'R', $fill);
            $pdf->Ln();

            $pdf->SetTextColor(0);
        }

        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($w[0], 6, '', 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 6, 'Totale ' . EURO, 'LR', 0, 'R', $fill);
        $pdf->Cell($w[2], 6, number_format($totaleDare, 2, ',', '.'), 'LR', 0, 'R', $fill);
        $pdf->Cell($w[3], 6, number_format($totaleAvere, 2, ',', '.'), 'LR', 0, 'R', $fill);
        $pdf->Cell($w[4], 6, number_format($saldo, 2, ',', '.'), 'LR', 0, 'R', $fill);
        $pdf->Ln();

        $pdf->Cell(array_sum($w), 0, '', 'T');
    }

}

?>