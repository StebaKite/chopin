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
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_CONTO) === NULL) {
            parent::setIndexSession(self::CANCELLA_CONTO, serialize(new CancellaConto()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_CONTO));
    }

    public function start() {
        $conto = Conto::getInstance();
        $db = Database::getInstance();

        $conto->cancella($db);

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(RicercaConto::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

    public function go() {
        $this->start();
    }

}