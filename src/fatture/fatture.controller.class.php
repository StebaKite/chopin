<?php

require_once 'fattura.class.php';
require_once 'dettaglioFattura.class.php';
require_once 'cliente.class.php';

class FattureController {

    public $fattureFunction = null;
    private $request;

    const MODO = "modo";
    const START = "start";

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
            if (isset($_REQUEST[self::MODO]))
                $this->setRequest($_REQUEST[self::MODO]);
            else
                $this->setRequest(self::START);
        }

        // Salvataggio dei campi della request negli oggetti di pertinenza

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();

        if (isset($_REQUEST["datafat"])) {
            $fattura->setDatFattura($_REQUEST["datafat"]);
            $fattura->setMeserif($_REQUEST["meserif"]);
            $fattura->setDesTitolo($_REQUEST["titolo"]);
            $fattura->setCatCliente("1200");
            $fattura->setDesCliente($_REQUEST["cliente"]);
            $fattura->setTipAddebito($_REQUEST["tipoadd"]);
            $fattura->setCodNegozio($_REQUEST["codneg"]);
            $fattura->setNumFattura($_REQUEST["numfat"]);
            $fattura->setDesRagsocBanca($_REQUEST["ragsocbanca"]);
            $fattura->setCodIbanBanca($_REQUEST["ibanbanca"]);
        }

        if (isset($_REQUEST["catcliente"])) {
            $fattura->setCatCliente($_REQUEST["catcliente"]);
            $fattura->setCodNegozio($_REQUEST["codneg"]);
        }

        if (isset($_REQUEST["idcliente"])) {
            $cliente->setIdCliente($_REQUEST["idcliente"]);
        }

        if (isset($_REQUEST["quantita"])) {
            $dettaglioFattura->setIdArticolo(rand(1, 99999));
            $dettaglioFattura->setQtaArticolo($_REQUEST["quantita"]);
            $dettaglioFattura->setDesArticolo($_REQUEST["articolo"]);
            $dettaglioFattura->setImpArticolo($_REQUEST["importo"]);
            $dettaglioFattura->setCodAliquota($_REQUEST["aliquota"]);
            $dettaglioFattura->setImpTotale($_REQUEST["totale"]);
            $dettaglioFattura->setImpImponibile($_REQUEST["imponibile"]);
            $dettaglioFattura->setImpIva($_REQUEST["iva"]);
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
