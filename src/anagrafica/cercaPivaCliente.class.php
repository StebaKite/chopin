<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';

class CercaPivaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CERCA_PIVA_CLIENTE])) {
            $_SESSION[self::CERCA_PIVA_CLIENTE] = serialize(new CercaPivaCliente());
        }
        return unserialize($_SESSION[self::CERCA_PIVA_CLIENTE]);
    }

    public function start() {
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();

        $cliente->cercaConDescrizione($db);
        $cliente->cercaPartivaIva($db);

        if ($cliente->getPivaEsistente() == "true") {
            echo "gi&agrave; esistente";
        } else {
            echo "";
        }
    }

    public function go() {
        
    }

}
