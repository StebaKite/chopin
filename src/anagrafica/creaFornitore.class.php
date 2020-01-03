<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
//require_once 'creaFornitore.template.php';
require_once 'ricercaFornitore.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class CreaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_FORNITORE) === NULL) {
            parent::setIndexSession(self::CREA_FORNITORE, serialize(new CreaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::CREA_FORNITORE));
    }

    public function start() {
        $fornitore = Fornitore::getInstance();
        $fornitore->prepara();
        echo $fornitore->getCodFornitore();
    }

    public function go() {
        $fornitore = Fornitore::getInstance();

        $db = Database::getInstance();
        $db->beginTransaction();

        if ($fornitore->inserisci($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaFornitore::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->start();
    }

}