<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'sottoconto.class.php';
require_once 'modificaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class InserisciSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::INSERISCI_SOTTOCONTO])) {
            $_SESSION[self::INSERISCI_SOTTOCONTO] = serialize(new InserisciSottoconto());
        }
        return unserialize($_SESSION[self::INSERISCI_SOTTOCONTO]);
    }

    public function start() {}

    public function go() {

        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();
        $sottoconto->aggiungi($db);

        $_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(ModificaConto::getInstance()));
        $controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
        $controller->start();
    }

}

?>