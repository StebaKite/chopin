<?php

require_once 'bilancio.class.php';
require_once 'riepilogo.class.php';

class RiepiloghiController {

    public $riepiloghiFunction = null;
    private $request;

    const MODO = "modo";
    const START = "start";

    /**
     * Oggetti
     */
    const BILANCIO = "Obj_bilancio";
    const RIEPILOGO = "Obj_riepilogo";

    /**
     * Metodi
     */
    public function __construct(RiepiloghiBusinessInterface $riepiloghiFunction) {
        $this->riepiloghiFunction = $riepiloghiFunction;
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

        $bilancio = Bilancio::getInstance();
        $riepilogo = Riepilogo::getInstance();

        if (isset($_REQUEST["anno_eserczio_sel"])) {
            $bilancio->setDataregDa("01/01/" . $_REQUEST["anno_eserczio_sel"]);
            $bilancio->setDataregA("31/12/" . $_REQUEST["anno_eserczio_sel"]);
            $bilancio->setAnnoEsercizioSel($_REQUEST["anno_eserczio_sel"]);
            $bilancio->setSoloContoEconomico("N");
            $bilancio->setCodnegSel($_REQUEST["codneg_sel"]);
            $bilancio->setSaldiInclusi($_REQUEST["saldiInclusi"]);
        }

        if (isset($_REQUEST["datareg_da"])) {
            $bilancio->setDataregDa($_REQUEST["datareg_da"]);
            $bilancio->setDataregA($_REQUEST["datareg_a"]);
            $bilancio->setCodnegSel($_REQUEST["codneg_sel"]);
            $bilancio->setCatconto($_REQUEST["catconto_sel"]);
            $bilancio->setSaldiInclusi($_REQUEST["saldiInclusi"]);
            $bilancio->setSoloContoEconomico($_REQUEST["soloContoEconomico"]);

            $riepilogo->setDataregDa($_REQUEST["datareg_da"]);
            $riepilogo->setDataregA($_REQUEST["datareg_a"]);
            $riepilogo->setSaldiInclusi($_REQUEST["saldiInclusi"]);
            $riepilogo->setSoloContoEconomico($_REQUEST["soloContoEconomico"]);
            $riepilogo->setCodnegSel($_REQUEST["codneg_sel"]);
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::BILANCIO] = serialize($bilancio);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);

        if ($this->getRequest() == "start") {
            $this->riepiloghiFunction->start();
        }
        if ($this->getRequest() == "go") {
            $this->riepiloghiFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}

?>