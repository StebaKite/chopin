<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'modificaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class ModificaGruppoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_GRUPPO_SOTTOCONTO) === NULL) {
            parent::setIndexSession(self::MODIFICA_GRUPPO_SOTTOCONTO, serialize(new ModificaGruppoSottoconto()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_GRUPPO_SOTTOCONTO));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();

        $sottoconto->aggiorna($db);
        echo $this->makeTabellaSottoconti($conto, $sottoconto);
    }

}