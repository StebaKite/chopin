<?php

require_once 'bilancio.class.php';
require_once 'riepilogo.class.php';
require_once 'nexus6.abstract.class.php';

class RiepiloghiController extends Nexus6Abstract {

    public $riepiloghiFunction = null;
    private $request;

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
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }

        $bilancio = Bilancio::getInstance();
        $riepilogo = Riepilogo::getInstance();

        if (null !== filter_input(INPUT_POST, "anno_eserczio_sel")) {
            $bilancio->setDataregDa("01/01/" . $this->getParmFromRequest("anno_eserczio_sel"));
            $bilancio->setDataregA("31/12/" . $this->getParmFromRequest("anno_eserczio_sel"));
            $bilancio->setAnnoEsercizioSel($this->getParmFromRequest("anno_eserczio_sel"));
            $bilancio->setSoloContoEconomico("N");
            $bilancio->setCodnegSel($this->getParmFromRequest("codneg_sel"));
            $bilancio->setSaldiInclusi($this->getParmFromRequest("saldiInclusi"));
        }

        if (null !== filter_input(INPUT_POST, "datareg_da")) {
            $bilancio->setDataregDa($this->getParmFromRequest("datareg_da"));
            $bilancio->setDataregA($this->getParmFromRequest("datareg_a"));
            $bilancio->setCodnegSel($this->getParmFromRequest("codneg_sel"));
            $bilancio->setCatconto($this->getParmFromRequest("catconto_sel"));
            $bilancio->setSaldiInclusi($this->getParmFromRequest("saldiInclusi"));
            $bilancio->setSoloContoEconomico($this->getParmFromRequest("soloContoEconomico"));
            $riepilogo->setDataregDa($this->getParmFromRequest("datareg_da"));
            $riepilogo->setDataregA($this->getParmFromRequest("datareg_a"));
            $riepilogo->setSaldiInclusi($this->getParmFromRequest("saldiInclusi"));
            $riepilogo->setSoloContoEconomico($this->getParmFromRequest("soloContoEconomico"));
            $riepilogo->setCodnegSel($this->getParmFromRequest("codneg_sel"));
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