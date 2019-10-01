<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';

class CercaCfisCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CERCA_CFISC_CLIENTE])) {
            $_SESSION[self::CERCA_CFISC_CLIENTE] = serialize(new CercaCfisCliente());
        }
        return unserialize($_SESSION[self::CERCA_CFISC_CLIENTE]);
    }

    public function start() {
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();

        $cliente->cercaCodiceFiscale($db);

        if ($cliente->getCfiscEsistente() == "true") {
            echo "gi&agrave; usato";
        } else {
            echo "";
        }
    }

    public function go() {
        
    }

}