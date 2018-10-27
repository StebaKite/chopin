<?php

class MainController {

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
            if (isset($_REQUEST["modo"]))
                $this->setRequest($_REQUEST["modo"]);
            else
                $this->setRequest("start");
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
