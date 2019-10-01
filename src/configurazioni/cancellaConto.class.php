<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configurazioni.controller.class.php';
require_once 'ricercaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';

class CancellaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_CONTO])) {
            $_SESSION[self::CANCELLA_CONTO] = serialize(new CancellaConto());
        }
        return unserialize($_SESSION[self::CANCELLA_CONTO]);
    }

    public function start() {
        $conto = Conto::getInstance();
        $db = Database::getInstance();

        $conto->cancella($db);

        $_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaConto::getInstance()));
        $controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
        $controller->start();
    }

    public function go() {
        $this->start();
    }

}