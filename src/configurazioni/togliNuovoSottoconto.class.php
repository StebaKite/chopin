<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'sottoconto.class.php';

class TogliNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::TOGLI_SOTTOCONTO) === NULL) {
            parent::setIndexSession(self::TOGLI_SOTTOCONTO, serialize(new TogliNuovoSottoconto()));
        }
        return unserialize(parent::getIndexSession(self::TOGLI_SOTTOCONTO));
    }

    public function start() {
        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();

        $sottoconto->cancella($db);
        parent::setIndexSession(self::SOTTOCONTO, serialize($sottoconto));

        echo $this->makeTabellaSottoconti($conto, $sottoconto);
    }

    public function go() {
        $this->start();
    }

}