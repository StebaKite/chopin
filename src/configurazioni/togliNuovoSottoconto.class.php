<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'sottoconto.class.php';

class TogliNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::TOGLI_SOTTOCONTO])) {
            $_SESSION[self::TOGLI_SOTTOCONTO] = serialize(new TogliNuovoSottoconto());
        }
        return unserialize($_SESSION[self::TOGLI_SOTTOCONTO]);
    }

    public function start() {
        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();

        $sottoconto->cancella($db);
        $_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

        echo $this->makeTabellaSottoconti($conto, $sottoconto);
    }

    public function go() {
        $this->start();
    }

}