<?php

require_once 'fattura.class.php';
require_once 'dettaglioFattura.class.php';
require_once 'cliente.class.php';
require_once 'nexus6.abstract.class.php';

class FattureController extends Nexus6Abstract {

    public $fattureFunction = null;
    private $request;

    /**
     * Metodi
     */
    public function __construct(FattureBusinessInterface $fattureFunction) {
        $this->fattureFunction = $fattureFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();

        if (null !== $this->getParmFromRequest(self::DATA_FATTURA)) {
            $fattura->setDatFattura($this->getParmFromRequest(self::DATA_FATTURA));
            $fattura->setMeserif($this->getParmFromRequest(self::MESE_RIFERIMENTO));
            $fattura->setDesTitolo($this->getParmFromRequest(self::TITOLO));
            $fattura->setDesCliente($this->getParmFromRequest(self::RAGIONE_SOCIALE_CLIENTE));
            $fattura->setTipAddebito($this->getParmFromRequest(self::TIPO_ADDEBITO));
            $fattura->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO));
            $fattura->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA));
            $fattura->setDesRagsocBanca($this->getParmFromRequest(self::RAGIONE_SOCIALE_BANCA_APPOGGIO));
            $fattura->setCodIbanBanca($this->getParmFromRequest(self::IBAN_BANCA_APPOGGIO));
            $fattura->setTipFattura($this->getParmFromRequest(self::TIPO_FATTURA));
            $fattura->setAssistito($this->getParmFromRequest(self::NOME_COGNOME_ASSISTITO));
        }

        if (null !== $this->getParmFromRequest(self::CATEGORIA_CLIENTE)) {
            $fattura->setCatCliente($this->getParmFromRequest(self::CATEGORIA_CLIENTE));
            $fattura->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO));
        }

        if (null !== $this->getParmFromRequest(self::ID_CLIENTE)) {
            $cliente->setIdCliente($this->getParmFromRequest(self::ID_CLIENTE));
        }

        if (null !== $this->getParmFromRequest(self::QUANTITA_ARTICOLO)) {
            $dettaglioFattura->setIdArticolo(rand());
            $dettaglioFattura->setQtaArticolo($this->getParmFromRequest(self::QUANTITA_ARTICOLO));
            $dettaglioFattura->setDesArticolo($this->getParmFromRequest(self::CODICE_ARTICOLO));
            $dettaglioFattura->setImpArticolo($this->getParmFromRequest(self::IMPORTO_UNITARIO));
            $dettaglioFattura->setCodAliquota($this->getParmFromRequest(self::ALIQUOTA_IVA));
            $dettaglioFattura->setImpTotale($this->getParmFromRequest(self::TOTALE_FATTURA));
            $dettaglioFattura->setImpImponibile($this->getParmFromRequest(self::IMPONIBILE_FATTURA));
            $dettaglioFattura->setImpIva($this->getParmFromRequest(self::IVA_FATTURA));
        }

        if (null !== $this->getParmFromRequest(self::ID_ARTICOLO)) {
            $dettaglioFattura->setIdArticolo($this->getParmFromRequest(self::ID_ARTICOLO));
        }

        // Serializzo in sessione gli oggetti modificati

        parent::setIndexSession(self::FATTURA, serialize($fattura));
        parent::setIndexSession(self::DETTAGLIO_FATTURA, serialize($dettaglioFattura));
        parent::setIndexSession(self::CLIENTE, serialize($cliente));

        if ($this->getRequest() == self::START) {
            $this->fattureFunction->start();
        }
        if ($this->getRequest() == self::GO) {
            $this->fattureFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}