<?php

require_once 'fattura.class.php';
require_once 'dettaglioFattura.class.php';
require_once 'cliente.class.php';
require_once 'nexus6.abstract.class.php';

class FattureController extends Nexus6Abstract {

    public $fattureFunction = null;
    private $request;

    /**
     * Oggetti
     */
    const FATTURA = "Obj_fattura";
    const DETTAGLIO_FATTURA = "Obj_dettagliofattura";
    const CLIENTE = "Obj_cliente";

    /**
     * Metodi
     */
    public function __construct(FattureBusinessInterface $fattureFunction) {
        $this->fattureFunction = $fattureFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();

        if (null !== filter_input(INPUT_POST, "datafat")) {
            $fattura->setDatFattura($this->getParmFromRequest("datafat"));
            $fattura->setMeserif($this->getParmFromRequest("meserif"));
            $fattura->setDesTitolo($this->getParmFromRequest("titolo"));
            $fattura->setDesCliente($this->getParmFromRequest("cliente"));
            $fattura->setTipAddebito($this->getParmFromRequest("tipoadd"));
            $fattura->setCodNegozio($this->getParmFromRequest("codneg"));
            $fattura->setNumFattura($this->getParmFromRequest("numfat"));
            $fattura->setDesRagsocBanca($this->getParmFromRequest("ragsocbanca"));
            $fattura->setCodIbanBanca($this->getParmFromRequest("ibanbanca"));
            $fattura->setTipFattura($this->getParmFromRequest("tipofat"));
            $fattura->setAssistito($this->getParmFromRequest("assistito"));
        }

        if (null !== filter_input(INPUT_POST, "catcliente")) {
            $fattura->setCatCliente($this->getParmFromRequest("catcliente"));
            $fattura->setCodNegozio($this->getParmFromRequest("codneg"));
        }

        if (null !== filter_input(INPUT_POST, "idcliente")) {
            $cliente->setIdCliente($this->getParmFromRequest("idcliente"));
        }

        if (null !== filter_input(INPUT_POST, "quantita")) {
            $dettaglioFattura->setIdArticolo(rand());
            $dettaglioFattura->setQtaArticolo($this->getParmFromRequest("quantita"));
            $dettaglioFattura->setDesArticolo($this->getParmFromRequest("articolo"));
            $dettaglioFattura->setImpArticolo($this->getParmFromRequest("importo"));
            $dettaglioFattura->setCodAliquota($this->getParmFromRequest("aliquota"));
            $dettaglioFattura->setImpTotale($this->getParmFromRequest("totale"));
            $dettaglioFattura->setImpImponibile($this->getParmFromRequest("imponibile"));
            $dettaglioFattura->setImpIva($this->getParmFromRequest("iva"));
        }

        if (null !== filter_input(INPUT_POST, "idarticolo")) {
            $dettaglioFattura->setIdArticolo($this->getParmFromRequest("idarticolo"));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::FATTURA] = serialize($fattura);
        $_SESSION[self::DETTAGLIO_FATTURA] = serialize($dettaglioFattura);
        $_SESSION[self::CLIENTE] = serialize($cliente);

        if ($this->getRequest() == "start") {
            $this->fattureFunction->start();
        }
        if ($this->getRequest() == "go") {
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