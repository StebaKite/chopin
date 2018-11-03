<?php

require_once 'nexus6.main.interface.php';

interface StrumentiBusinessInterface extends MainNexus6Interface {

    // Oggetti

    const CAMBIA_CONTO_STEP1 = "Obj_cambiacontostep1";
    const CAMBIA_CONTO_STEP2 = "Obj_cambiacontostep2";
    const CAMBIA_CONTO_STEP3 = "Obj_cambiacontostep3";
    
    // Actions

    const AZIONE_RICERCA_REGISTRAZIONE = "../strumenti/cambiaContoStep1Facade.class.php?modo=go";
    const AZIONE_CAMBIA_CONTO_STEP2 = "../strumenti/cambiaContoStep3Facade.class.php?modo=start";
    const AZIONE_CAMBIA_CONTO_STEP3 = "../strumenti/cambiaContoStep3Facade.class.php?modo=go";

    // Errori e messaggi
    // Metodi

    public static function getInstance();

    public function start();

    public function go();
}

?>
