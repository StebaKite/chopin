<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIUNGI_SOTTOCONTO) === NULL) {
            parent::setIndexSession(self::AGGIUNGI_SOTTOCONTO, serialize(new AggiungiNuovoSottoconto()));
        }
        return unserialize(parent::getIndexSession(self::AGGIUNGI_SOTTOCONTO));
    }

    public function start() {
        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $sottoconto->setCodConto($conto->getCodConto());

        $sottoconto->aggiungi();
        parent::setIndexSession(self::SOTTOCONTO, serialize($sottoconto));

        echo $this->makeTabellaSottoconti($conto, $sottoconto);
    }

    public function go() {
        $this->start();
    }

}