<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class CreaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_CAUSALE) === NULL) {
            parent::setIndexSession(self::CREA_CAUSALE, serialize(new CreaCausale()));
        }
        return unserialize(parent::getIndexSession(self::CREA_CAUSALE));
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

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(RicercaCausale::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

}