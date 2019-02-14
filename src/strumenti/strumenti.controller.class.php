<?php

require_once 'registrazione.class.php';
require_once 'conto.class.php';
require_once 'corrispettivo.class.php';

class StrumentiController {
    
    public $strumentiFunction = null;
    private $request;

    // Oggetti

    const REGISTRAZIONE = "Obj_registrazione";
    const CONTO = "Obj_conto";
    const CORRISPETTIVO = "Obj_corrispettivo";

    // Metodi
    
    public function __construct(StrumentiBusinessInterface $strumentiFunction) {
        $this->strumentiFunction = $strumentiFunction;
        $this->setRequest(null);
    }
    
    public function start() {

        if ($this->getRequest() == null) {
            if (isset($_REQUEST["modo"]))
                $this->setRequest($_REQUEST["modo"]);
            else
                $this->setRequest("start");
        }
     
        $registrazione = Registrazione::getInstance();
        $conto = Conto::getInstance();
        $corrispettivo = Corrispettivo::getInstance();

        // Cambia conto registrazioni ==============================================================

        if (isset($_REQUEST["datareg_da"])) {
            $registrazione->setDatRegistrazioneDa($_REQUEST["datareg_da"]);
            $registrazione->setDatRegistrazioneA($_REQUEST["datareg_a"]);
            $registrazione->setCodNegozioSel($_REQUEST["codneg_sel"]);
            $registrazione->setCodContoSel($_REQUEST["codconto_sel"]);
            $conto->setCodContoSel($_REQUEST["codconto_sel"]);
        }

        if (isset($_REQUEST["conto_sel_nuovo"])) {
            $conto->setCodContoSelNuovo($_REQUEST["conto_sel_nuovo"]);
        }

        if (isset($_REQUEST["contocassa"])) {
            
            $corrispettivo->setMese($_REQUEST["mese"]);
            $corrispettivo->setAnno($_REQUEST["anno"]);
            $corrispettivo->setCodNeg($_REQUEST["codneg"]);
            $corrispettivo->setFile($_REQUEST["file"]);
            $corrispettivo->setDatada($_REQUEST["datada"]);
            $corrispettivo->setDataa($_REQUEST["dataa"]);
            $corrispettivo->setContoCassa($_REQUEST["contocassa"]);
        }
        
        // Serializzo in sessione gli oggetti modificati ========================================

        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
        $_SESSION[self::CONTO] = serialize($conto);
        $_SESSION[self::CORRISPETTIVO] = serialize($corrispettivo);
        
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
