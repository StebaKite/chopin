<?php

require_once 'nexus6.abstract.class.php';

class MainController extends Nexus6Abstract {

    public $mainFunction = null;
    private $request;

    public function __construct(MainBusinessInterface $mainFunction) {
        $this->mainFunction = $mainFunction;
        $this->setRequest(null);
    }

    public function start() {
        
        $this->setRequest(self::START);         // default set

        if ($this->getRequest() == self::START) {
            $this->mainFunction->start();
        }
        if ($this->getRequest() == self::GO) {
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
