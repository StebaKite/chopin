<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'ricercaCliente.class.php';
require_once 'cliente.class.php';

class CancellaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_CLIENTE) === NULL) {
            parent::setIndexSession(self::CANCELLA_CLIENTE, serialize(new CancellaCliente()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_CLIENTE));
    }

    public function start() {

        $cliente = Cliente::getInstance();
        $db = Database::getInstance();

        $cliente->cancella($db);

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaCliente::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->start();
    }

    public function go() {
        
    }

}