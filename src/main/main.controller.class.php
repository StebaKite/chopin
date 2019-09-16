<?php

require_once 'nexus6.abstract.class.php';

class MainController extends Nexus6Abstract {

    public $mainFunction = null;
    private $request;

    // Oggetti

    const CONTROLLI_APERTURA = "Obj_controlliapertura";

    public function __construct(MainBusinessInterface $mainFunction) {
        $this->mainFunction = $mainFunction;
        $this->setRequest(null);
    }

    public function start() {
        
        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }
        

        if ($this->getRequest() == "start") {
            $this->mainFunction->start();
        }
        if ($this->getRequest() == "go") {
            $this->mainFunction->go();
        }
    }
    
    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}
