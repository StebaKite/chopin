<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.extractor.interface.php';
require_once 'utility.class.php';
require_once 'pdf.class.php';
require_once 'bilancio.class.php';

class EstraiPdfBilancio extends RiepiloghiAbstract implements RiepiloghiBusinessInterface {

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
        if (parent::getIndexSession(self::ESTRAI_PDF_BILANCIO) === NULL) {
            parent::setIndexSession(self::ESTRAI_PDF_BILANCIO, serialize(new EstraiPdfBilancio()));
        }
        return unserialize(parent::getIndexSession(self::ESTRAI_PDF_BILANCIO));
    }

    public function start() {

        $bilancio = Bilancio::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $pdf = Pdf::getInstance();
        $pdf->setLogo($this->root . $array["logo"]);
        $pdf->setCreator($array["productName"]);

        if ($bilancio->getTipoBilancio() == self::ESERCIZIO) {
            $pdf->setTitle("BILANCIO ESERCIZIO");
            $pdf->setTitle1("Anno " . $bilancio->getAnnoEsercizioSel());
        }

        if ($bilancio->getTipoBilancio() == self::PERIODICO) {
            $pdf->setTitle("BILANCIO ESERCIZIO");
            $pdf->setTitle1("Dal " . $bilancio->GetDataregDa() . " al " . $bilancio->GetDataregA());
        }

        $pdf->AliasNbPages();

        /**
         * Generazione del documento
         */
        $pdf = $this->generaSezioneIntestazione($pdf, $bilancio);
        $pdf = $this->generaSezioneTabellaBilancio($pdf, $bilancio);

        if ($bilancio->getSoloContoEconomico() == "N") {
            $pdf = $this->generaSezioneIntestazione($pdf, $bilancio);
            $pdf = $this->generaSezioneTabellaBilancioEsercizio($pdf, $bilancio);
        }

        $pdf->Output();
    }

    public function generaSezioneIntestazione($pdf, $bilancio) {

        if ($bilancio->getCodnegSel() != "CAS") {

            if ($bilancio->getCodnegSel() != "") {
                $negozio = "";
                $negozio = ($bilancio->getCodnegSel() == "ERB") ? "Erba" : $negozio;

                $pdf->setTitle2("Negozio di " . $negozio);
            } else {
                $pdf->setTitle2("Tutti i negozi");
            }
        } else {
            $pdf->setTitle2("");
        }

        return $pdf;
    }

    private function generaSezioneTabellaBilancio($pdf, $bilancio) {

        $fill = true;

        /**
         * Costi
         */
        if ($bilancio->getNumCostiTrovati() > 0) {

            $pdf->AddPage();
            $pdf->SetFont('', 'B', 12);
            $pdf->SetFillColor(171, 224, 245);
            $pdf->Cell($w[0], 6, 'COSTI' . str_repeat(' ', 87) . 'Parziale ' . EURO . str_repeat(' ', 19) . 'Totale ' . EURO, '', 0, 'L', $fill);
            $pdf->Ln();
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 11);

            $pdf->BilancioTable($bilancio->getCostiBilancio(), 1);
            $pdf->TotaleCostiTable($bilancio->getTotaleCosti());
        }

        /**
         * Ricavi
         */
        if ($bilancio->getNumRicaviTrovati() > 0) {

            $pdf->AddPage();
            $pdf->SetFont('', 'B', 12);
            $pdf->SetFillColor(171, 224, 245);
            $pdf->Cell($w[0], 6, 'RICAVI' . str_repeat(' ', 87) . 'Parziale ' . EURO . str_repeat(' ', 19) . 'Totale ' . EURO, '', 0, 'L', $fill);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 11);

            $pdf->BilancioTableRicavi($bilancio->getRicaviBilancio(), -1);
            $pdf->TotaleRicaviTable(abs($bilancio->getTotaleRicavi()));
        }

        /**
         * Riepilogo totali
         */
        if (($bilancio->getNumCostiTrovati() > 0) or ( $bilancio->getNumRicaviTrovati() > 0)) {

            if (abs($bilancio->getTotaleRicavi()) >= abs($bilancio->getTotaleCosti())) {
                $pdf->BilancioCostiTable(abs($bilancio->getTotaleRicavi()), abs($bilancio->getTotaleCosti()));
            } else {
                if (abs($bilancio->getTotaleRicavi()) < abs($bilancio->getTotaleCosti())) {
                    $pdf->BilancioRicaviTable(abs($bilancio->getTotaleRicavi()), abs($bilancio->getTotaleCosti()));
                }
            }
        }

        return $pdf;
    }

    private function generaSezioneTabellaBilancioEsercizio($pdf, $bilancio) {

        $fill = true;

        /**
         * Attivo
         */
        if ($bilancio->getNumAttivoTrovati() > 0) {

            $pdf->AddPage();
            $pdf->SetFont('', 'B', 12);
            $pdf->SetFillColor(171, 224, 245);
            $pdf->Cell($w[0], 6, "ATTIVITA'" . str_repeat(' ', 82) . 'Parziale ' . EURO . str_repeat(' ', 18) . 'Totale ' . EURO, '', 0, 'L', $fill);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetFont('Arial', '', 11);

            $pdf->BilancioEsercizioTable($bilancio->getAttivoBilancio());
            $pdf->TotaleAttivoTable(abs($bilancio->getNumAttivoTrovati()));
        }

        /**
         * Passivo
         */
        if ($bilancio->getNumPassivoTrovati() > 0) {

            $pdf->AddPage();
            $pdf->SetFont('', 'B', 12);
            $pdf->SetFillColor(171, 224, 245);
            $pdf->Cell($w[0], 6, "PASSIVITA'" . str_repeat(' ', 80) . 'Parziale ' . EURO . str_repeat(' ', 18) . 'Totale ' . EURO, '', 0, 'L', $fill);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetFont('Arial', '', 11);

            $pdf->BilancioEsercizioTable($bilancio->getPassivoBilancio());
            $pdf->TotalePassivoTable(abs($bilancio->getTotalePassivo()));
        }

        return $pdf;
    }

    public function go() {

    }

}

?>