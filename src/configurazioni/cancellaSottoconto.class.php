<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'modificaConto.class.php';
require_once 'sottoconto.class.php';

class CancellaSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_SOTTOCONTO])) {
            $_SESSION[self::CANCELLA_SOTTOCONTO] = serialize(new CancellaSottoconto());
        }
        return unserialize($_SESSION[self::CANCELLA_SOTTOCONTO]);
    }

    public function start() {
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();
        $sottoconto->cancella($db);

        $_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(ModificaConto::getInstance()));
        $controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
        $controller->start();
    }

    public function go() {
        $this->start();
    }

}