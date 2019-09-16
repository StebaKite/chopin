<?php

require_once 'saldo.class.php';
require_once 'nexus6.abstract.class.php';

class SaldiController extends Nexus6Abstract {
    
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
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }
     
        $saldo = Saldo::getInstance();

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
