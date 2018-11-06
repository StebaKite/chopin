<?php

require_once 'saldo.class.php';

class SaldiController {
    
    public $saldiFunction = null;
    private $request;

    // Oggetti

    const SALDO = "Obj_saldo";

    // Metodi
    
    public function __construct(SaldiBusinessInterface $saldiFunction) {
        $this->saldiFunction = $saldiFunction;
        $this->setRequest(null);
    }
    
    public function start() {

        if ($this->getRequest() == null) {
            if (isset($_REQUEST["modo"]))
                $this->setRequest($_REQUEST["modo"]);
            else
                $this->setRequest("start");
        }
     
        $saldo = Saldo::getInstance();

        // Serializzo in sessione gli oggetti modificati ========================================

        $_SESSION[self::SALDO] = serialize($saldo);
        
        if ($this->getRequest() == "start") {
            $this->saldiFunction->start();
        }
        if ($this->getRequest() == "go") {
            $this->saldiFunction->go();
        }
    }    
 
    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }   
}
