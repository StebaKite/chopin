<?php

interface MainBusinessInterface {

    // Oggetti

    const MAIN = "Obj_main";
    const CONTROLLI_APERTURA = "Obj_controlliapertura";
    const XML_CONTROLLI_APERTURA = "/main/xml/controlliApertura.xml";

    // Metodi

    public static function getInstance();

    public function start();

    public function go();
}

?>