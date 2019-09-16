<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_SOTTOCONTO])) {
            $_SESSION[self::AGGIUNGI_SOTTOCONTO] = serialize(new AggiungiNuovoSottoconto());
        }
        return unserialize($_SESSION[self::AGGIUNGI_SOTTOCONTO]);
    }

    public function start() {
        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $sottoconto->setCodConto($conto->getCodConto());

        $sottoconto->aggiungi();
        $_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

        echo $this->makeTabellaSottoconti($conto, $sottoconto);
    }

    public function go() {
        $this->start();
    }

}