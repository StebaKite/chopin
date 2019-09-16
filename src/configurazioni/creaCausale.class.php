<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class CreaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CREA_CAUSALE])) {
            $_SESSION[self::CREA_CAUSALE] = serialize(new CreaCausale());
        }
        return unserialize($_SESSION[self::CREA_CAUSALE]);
    }

    public function start() {
        $causale = Causale::getInstance();
        $causale->prepara();
    }

    public function go() {
        $causale = Causale::getInstance();

        $db = Database::getInstance();
        $db->beginTransaction();

        if ($causale->inserisci($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        $_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));
        $controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
        $controller->start();
    }

}