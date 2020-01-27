<?php

require_once 'nexus6.main.interface.php';

interface StrumentiBusinessInterface extends MainNexus6Interface {

    // Oggetti

    const CAMBIA_CONTO_STEP1 = "Obj_cambiacontostep1";
    const CAMBIA_CONTO_STEP2 = "Obj_cambiacontostep2";
    const CAMBIA_CONTO_STEP3 = "Obj_cambiacontostep3";
//    const CORRISPETTIVO = "Obj_corrispettivo";
//    const REGISTRAZIONE = "Obj_registrazione";
//    const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";

    const IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1 = "Obj_importacorrispettivinegoziostep1";
    const IMPORTA_CORRISPETTIVI_NEGOZIO_STEP2 = "Obj_importacorrispettivinegoziostep2";
    const IMPORTA_PRESENZE_ASSISTITI_STEP1 = "Obj_importapresenzaassistitistep1";
    const IMPORTA_PRESENZE_ASSISTITI_STEP2 = "Obj_importapresenzaassistitistep2";
    
    // Actions

    const AZIONE_RICERCA_REGISTRAZIONE = "../strumenti/cambiaContoStep1Facade.class.php?modo=go";
    const AZIONE_CAMBIA_CONTO_STEP2 = "../strumenti/cambiaContoStep3Facade.class.php?modo=start";
    const AZIONE_CAMBIA_CONTO_STEP3 = "../strumenti/cambiaContoStep3Facade.class.php?modo=go";

    const AZIONE_IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1 = "../strumenti/importaExcelCorrispettiviNegozioStep1Facade.class.php?modo=go";
    const AZIONE_IMPORTA_CORRISPETTIVI_NEGOZIO_STEP2 = "../strumenti/importaExcelCorrispettiviNegozioStep2Facade.class.php?modo=go";

    const AZIONE_IMPORTA_PRESENZE_ASSISTITI_STEP1 = "../strumenti/importaExcelPresenzeAssistitiStep1Facade.class.php?modo=go";
    const AZIONE_IMPORTA_PRESENZE_ASSISTITI_STEP2 = "../strumenti/importaExcelPresenzeAssistitiStep2Facade.class.php?modo=go";

    // Errori e messaggi
    // Metodi

    public static function getInstance();

    public function start();

    public function go();
}

?>
