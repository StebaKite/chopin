<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'ricercaCliente.class.php';
require_once 'cliente.class.php';

class CancellaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_CLIENTE])) {
            $_SESSION[self::CANCELLA_CLIENTE] = serialize(new CancellaCliente());
        }
        return unserialize($_SESSION[self::CANCELLA_CLIENTE]);
    }

    public function start() {

        $cliente = Cliente::getInstance();
        $db = Database::getInstance();

        $cliente->cancella($db);

        $_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaCliente::getInstance()));
        $controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
        $controller->start();
    }

    public function go() {
        
    }

}