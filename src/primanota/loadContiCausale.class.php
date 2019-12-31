<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class LoadContiCausale extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::LOAD_CONTI_CAUSALE) === NULL) {
            parent::setIndexSession(self::LOAD_CONTI_CAUSALE, serialize(new LoadContiCausale()));
        }
        return unserialize(parent::getIndexSession(self::LOAD_CONTI_CAUSALE));
    }

    public function start() {
        $causale = Causale::getInstance();
        $db = Database::getInstance();
        $causale->loadContiConfigurati($db);
        echo $causale->getContiCausale();
    }

    public function go() {
        $this->start();
    }

}