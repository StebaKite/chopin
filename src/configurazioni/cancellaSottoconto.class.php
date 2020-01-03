<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'modificaConto.class.php';
require_once 'sottoconto.class.php';

class CancellaSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_SOTTOCONTO) === NULL) {
            parent::setIndexSession(self::CANCELLA_SOTTOCONTO, serialize(new CancellaSottoconto()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_SOTTOCONTO));
    }

    public function start() {
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();
        $sottoconto->cancella($db);

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(ModificaConto::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

    public function go() {
        $this->start();
    }

}