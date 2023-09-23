<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'pdf.class.php';
require_once 'scadenzaCliente.class.php';

class EstraiPdfScadenzeCliente extends ScadenzeAbstract implements ScadenzeBusinessInterface {

    public static $azioneRicercaScadenzeCliente = "../scadenze/ricercaScadenzeClienteFacade.class.php?modo=go";
    public static $queryRicercaScadenzeCliente = "/scadenze/ricercaScadenzeCliente.sql";

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
        if (parent::getIndexSession(self::ESTRAI_PDF_SCADENZE_CLIENTE) === NULL) {
            parent::setIndexSession(self::ESTRAI_PDF_SCADENZE_CLIENTE, serialize(new EstraiPdfScadenzeCliente()));
        }
        return unserialize(parent::getIndexSession(self::ESTRAI_PDF_SCADENZE_CLIENTE));
    }

    public function start() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $pdf = Pdf::getInstance();

        $pdf->setLogo($this->root . $array[self::LOGO]);
        $pdf->setCreator(self::NEXUS6);
        $pdf->AliasNbPages();

        /**
         * Generazione del documento
         */
        $pdf = $this->generaSezioneIntestazione($pdf);
        $pdf = $this->generaSezioneTabellaScadenze($pdf, $utility);

        $pdf->Output();
    }

    public function go() {
        
    }

    public function generaSezioneIntestazione($pdf) {
        $scadenzaCliente = ScadenzaCliente::getInstance();

        $pdf->setTitle("Scadenze clienti dal " . $scadenzaCliente->getDatScadenzaDa() . " al " . $scadenzaCliente->getDatScadenzaA());

        $negozio = self::EMPTYSTRING;
        $negozio = ($scadenzaCliente->getCodNegozioSel() == self::ERBA) ? self::NEGOZIO_ERBA : $negozio;

        if ($negozio != self::EMPTYSTRING)
            $pdf->setTitle1("Negozio di " . $negozio);
        else
            $pdf->setTitle1("Tutti i negozi");

        return $pdf;
    }

    public function generaSezioneTabellaScadenze($pdf, $utility) {
        $scadenzaCliente = ScadenzaCliente::getInstance();

        $pdf->AddPage('L');

        $header = array("Data", "Cliente", "Nota", "Tipo Addebito", "Stato", "Importo");
        $pdf->SetFont('Arial', '', 9);
        $pdf->ScadenzeClientiTable($header, $scadenzaCliente->getScadenze());

        return $pdf;
    }

}