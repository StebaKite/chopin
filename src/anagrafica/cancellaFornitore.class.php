<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'ricercaFornitore.class.php';
require_once 'fornitore.class.php';

class CancellaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_FORNITORE])) {
            $_SESSION[self::CANCELLA_FORNITORE] = serialize(new CancellaFornitore());
        }
        return unserialize($_SESSION[self::CANCELLA_FORNITORE]);
    }

    public function start() {

        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();

        $fornitore->cancella($db);

        $_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaFornitore::getInstance()));
        $controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
        $controller->start();
    }

    public function go() {
        
    }

}