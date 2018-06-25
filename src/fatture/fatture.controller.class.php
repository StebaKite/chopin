<?php

require_once 'fattura.class.php';

class FattureController {

    public $fattureFunction = null;
    private $request;

    const MODO = "modo";
    const START = "start";

    /**
     * Oggetti
     */
    const FATTURA = "Obj_fattura";

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

        if (isset($_REQUEST["datafat"])) {
            $fattura->setDatafat($_REQUEST["datafat"]);
            $fattura->setMeserif($_REQUEST["meserif"]);
            $fattura->setTitolo($_REQUEST["titolo"]);
            $fattura->setCatCliente("1200");
            $fattura->setCliente($_REQUEST["cliente"]);
            $fattura->setTipoadd($_REQUEST["tipoadd"]);
            $fattura->setCodneg($_REQUEST["codneg"]);
            $fattura->setNumfat($_REQUEST["numfat"]);
            $fattura->setRagsocBanca($_REQUEST["ragsocbanca"]);
            $fattura->setIbanBanca($_REQUEST["ibanbanca"]);
            $fattura->setDettagliInseriti($_REQUEST["dettagliInseriti"]);
            $fattura->setIndexDettagliInseriti($_REQUEST["indexDettagliInseriti"]);
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::FATTURA] = serialize($fattura);

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
