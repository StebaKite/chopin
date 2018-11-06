<?php

require_once 'nexus6.main.interface.php';

interface SaldiBusinessInterface extends MainNexus6Interface {

    // Oggetti

    const CAMBIA_CONTO_STEP1 = "Obj_cambiacontostep1";
    
    // Actions
    // Errori e messaggi
    // Metodi

    public static function getInstance();

    public function start();

    public function go();
}

?>
