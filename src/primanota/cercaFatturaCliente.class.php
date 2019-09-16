<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';

class CercaFatturaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CERCA_FATTURA_CLIENTE])) {
            $_SESSION[self::CERCA_FATTURA_CLIENTE] = serialize(new CercaFatturaCliente());
        }
        return unserialize($_SESSION[self::CERCA_FATTURA_CLIENTE]);
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($registrazione->cercaFatturaCliente($db))
            echo "esistente";
        else
            echo " ";
    }

    public function go() {
        $this->start();
    }

}