<?php

require_once 'saldo.class.php';
require_once 'nexus6.abstract.class.php';

class SaldiController extends Nexus6Abstract {
    
    public $saldiFunction = null;
    private $request;

    // Metodi
    
    public function __construct(SaldiBusinessInterface $saldiFunction) {
        $this->saldiFunction = $saldiFunction;
        $this->setRequest(null);
    }
    
    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }
     
        $saldo = Saldo::getInstance();

        $_SESSION[self::SALDO] = serialize($saldo);
        
        if ($this->getRequest() == self::START) {
            $this->saldiFunction->start();
        }
        if ($this->getRequest() == self::GO) {
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
