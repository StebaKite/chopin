<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configurazioni.controller.class.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class CancellaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_CAUSALE) === NULL) {
            parent::setIndexSession(self::CANCELLA_CAUSALE, serialize(new CancellaCausale()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_CAUSALE));
    }

    public function start() {}

    public function go() {
        $causale = Causale::getInstance();
        $db = Database::getInstance();
        $causale->cancella($db);

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(RicercaCausale::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

}