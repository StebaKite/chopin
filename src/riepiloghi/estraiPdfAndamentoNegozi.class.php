<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.extractor.interface.php';
require_once 'utility.class.php';
require_once 'pdf.class.php';
require_once 'riepilogo.class.php';

class EstraiPdfAndamentoNegozi extends RiepiloghiAbstract implements RiepiloghiBusinessInterface {

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
        if (parent::getIndexSession(self::ESTRAI_PDF_ANDAMENTO_NEGOZIO) === NULL) {
            parent::setIndexSession(self::ESTRAI_PDF_ANDAMENTO_NEGOZIO, serialize(new EstraiPdfAndamentoNegozi()));
        }
        return unserialize(parent::getIndexSession(self::ESTRAI_PDF_ANDAMENTO_NEGOZIO));
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
        $pdf = $this->generaSezioneTabellaProgressivi($pdf, $riepilogo);
        $pdf = $this->generaSezioneTabellaUtilePerdita($pdf, $riepilogo);
        $pdf = $this->generaSezioneTabellaMctProgressivi($pdf, $riepilogo);

        $pdf->Output();
    }

    public function go() {

    }

    public function generaSezioneIntestazione($pdf, $riepilogo) {

        $pdf->setTitle("Progressivi Mensili Chopin");
        $pdf->setTitle1("Dal " . $riepilogo->getDataregDa() . " al " . $riepilogo->getDataregA());

        if (parent::isNotEmpty($riepilogo->getCodnegSel())) {
            $negozio = ($riepilogo->getCodnegSel() == self::VILLA) ? "Villa D'Adda" : $negozio;
            $negozio = ($riepilogo->getCodnegSel() == self::BREMBATE) ? "Brembate" : $negozio;
            $negozio = ($riepilogo->getCodnegSel() == self::TREZZO) ? "Trezzo" : $negozio;
            $pdf->setTitle1("Negozio di " . $negozio);
        }
        return $pdf;
    }

    private function generaSezioneTabellaProgressivi($pdf, $riepilogo) {

        $pdf->AddPage('L');

        $headerCosti = array("Costi", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->progressiviNegozioTable($headerCosti, $riepilogo->getCostiAndamentoNegozio(), 1);

        $pdf->AddPage('L');

        $headerRicavi = array("Ricavi", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
        $pdf->progressiviNegozioTable($headerRicavi, $riepilogo->getRicaviAndamentoNegozio(), -1);

        return $pdf;
    }

    private function generaSezioneTabellaUtilePerdita($pdf, $riepilogo) {

        $pdf->AddPage('L');

        $header = array("Utile/Perdita", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
        $pdf->progressiviUtilePerditaTable($header, $riepilogo->getTotaliComplessiviAcquistiMesi(), $riepilogo->getTotaliComplessiviRicaviMesi());

        return $pdf;
    }

    private function generaSezioneTabellaMctProgressivi($pdf, $riepilogo) {

        $pdf->Cell(100, 10, '', '', 0, 'R', $fill);
        $pdf->Ln();

        $header = array("Margine di Contribuzione", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
        $pdf->SetFont('Arial', '', 9);
        $pdf->progressiviMctTable($header, $riepilogo->getTotaliAcquistiMesi(), $riepilogo->getTotaliRicaviMesi());

        return $pdf;
    }

}

?>
