<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'sottoconto.class.php';
require_once 'modificaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class InserisciSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::INSERISCI_SOTTOCONTO) === NULL) {
            parent::setIndexSession(self::INSERISCI_SOTTOCONTO, serialize(new InserisciSottoconto()));
        }
        return unserialize(parent::getIndexSession(self::INSERISCI_SOTTOCONTO));
    }

    public function start() {}

    public function go() {

        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();
        $sottoconto->aggiungi($db);

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(ModificaConto::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

}

?>