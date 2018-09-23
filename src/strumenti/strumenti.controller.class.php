<?php

require_once 'mercato.class.php';
require_once 'negozio.class.php';

class StrumentiController {

    public $strumentiFunction = null;
    private $request;

    const MODO = "modo";
    const START = "start";

    /**
     * Oggetti
     */
    const MERCATO = "Obj_mercato";
    const NEGOZIO = "Obj_mercato";

    /**
     * Metodi
     */
    public function __construct(StrumentiBusinessInterface $strumentiFunction) {
        $this->strumentiFunction = $strumentiFunction;
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

        $mercato = Mercato::getInstance();
        $negozio = Negozio::getInstance();

        if (isset($_REQUEST["file"])) {
            $mercato->setCodNegozio($_REQUEST["codneg"]);
            $mercato->setCodMercato($_REQUEST["mercato"]);
            $mercato->setFile($_REQUEST["file"]);
            $mercato->setMese($_REQUEST["mese"]);
            $mercato->setAnno($_REQUEST["anno"]);
            $mercato->setDatada($_REQUEST["datada"]);
            $mercato->setDataa($_REQUEST["dataa"]);

            $negozio->setCodNegozio($_REQUEST["codneg"]);
            $negozio->setFile($_REQUEST["file"]);
            $negozio->setMese($_REQUEST["mese"]);
            $negozio->setAnno($_REQUEST["anno"]);
            $negozio->setDatada($_REQUEST["datada"]);
            $negozio->setDataa($_REQUEST["dataa"]);
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::MERCATO] = serialize($mercato);
        $_SESSION[self::NEGOZIO] = serialize($negozio);

        if ($this->getRequest() == "start") {
            $this->strumentiFunction->start();
        }
        if ($this->getRequest() == "go") {
            $this->strumentiFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}
