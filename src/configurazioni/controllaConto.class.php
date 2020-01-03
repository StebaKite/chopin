<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaConto.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';

class ControllaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CONTROLLA_CONTO) === NULL) {
            parent::setIndexSession(self::CONTROLLA_CONTO, serialize(new ControllaConto()));
        }
        return unserialize(parent::getIndexSession(self::CONTROLLA_CONTO));
    }

    public function start() {
        $conto = Conto::getInstance();
        $db = Database::getInstance();

        if ($conto->leggi($db) > 0) {
            echo "Conto presente";
        } else {
            echo "";
        }
    }

    public function go() {
        $this->start();
    }

}