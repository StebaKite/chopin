<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'ricercaFornitore.class.php';
require_once 'fornitore.class.php';

class CancellaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_FORNITORE) === NULL) {
            parent::setIndexSession(self::CANCELLA_FORNITORE, serialize(new CancellaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_FORNITORE));
    }

    public function start() {

        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();

        $fornitore->cancella($db);

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaFornitore::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->start();
    }

    public function go() {
        
    }

}