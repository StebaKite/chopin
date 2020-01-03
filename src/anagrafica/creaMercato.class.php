<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'mercato.class.php';
require_once 'ricercaMercato.class.php';
require_once 'anagrafica.controller.class.php';

class CreaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_MERCATO) === NULL) {
            parent::setIndexSession(self::CREA_MERCATO, serialize(new CreaMercato()));
        }
        return unserialize(parent::getIndexSession(self::CREA_MERCATO));
    }

    public function start() {
        
    }

    public function go() {
        $mercato = Mercato::getInstance();

        $db = Database::getInstance();
        $db->beginTransaction();

        if ($mercato->nuovo($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaMercato::getInstance())));

        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->setRequest("start");
        $controller->start();
    }

}