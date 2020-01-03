<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'mercato.class.php';
require_once 'ricercaMercato.class.php';
require_once 'anagrafica.controller.class.php';

class CancellaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_MERCATO) === NULL) {
            parent::setIndexSession(self::CANCELLA_MERCATO, serialize(new CancellaMercato()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_MERCATO));
    }

    public function start() {

        $mercato = Mercato::getInstance();
        $db = Database::getInstance();

        $mercato->cancella($db);

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaMercato::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->setRequest("start");
        $controller->start();
    }

    public function go() {
        
    }

}