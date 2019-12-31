<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';

class CercaFatturaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CERCA_FATTURA_CLIENTE) === NULL) {
            parent::setIndexSession(self::CERCA_FATTURA_CLIENTE, serialize(new CercaFatturaCliente()));
        }
        return unserialize(parent::getIndexSession(self::CERCA_FATTURA_CLIENTE));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $db = Database::getInstance();

        if ($registrazione->cercaFatturaCliente($db))
            echo "esistente";
        else
            echo " ";
    }

    public function go() {
        $this->start();
    }

}