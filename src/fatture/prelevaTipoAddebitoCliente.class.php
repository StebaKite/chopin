<?php

require_once 'fattura.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'fatture.business.interface.php';

class PrelevaTipoAddebitoCliente extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {

        self::$root = $_SERVER['DOCUMENT_ROOT'];
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
    }

    public function getInstance() {

        if (!isset($_SESSION[self::PRELEVA_TIPO_ADDEBITO_CLIENTE]))
            $_SESSION[self::PRELEVA_TIPO_ADDEBITO_CLIENTE] = serialize(new PrelevaTipoAddebitoCliente());
        return unserialize($_SESSION[self::PRELEVA_TIPO_ADDEBITO_CLIENTE]);
    }

    public function start() {

        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $cliente->caricaTipoAddebitoCliente($db);

        echo $cliente->getTipAddebito();
    }

    public function go() {

    }

}

?>