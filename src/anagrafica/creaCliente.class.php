<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
require_once 'ricercaCliente.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';
require_once 'categoriaCliente.class.php';

class CreaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_CLIENTE) === NULL) {
            parent::setIndexSession(self::CREA_CLIENTE, serialize(new CreaCliente()));
        }
        return unserialize(parent::getIndexSession(self::CREA_CLIENTE));
    }

    public function start() {
        $cliente = Cliente::getInstance();
        $cliente->prepara();
        echo $cliente->getCodCliente();
    }

    public function go() {
        $cliente = Cliente::getInstance();

        $db = Database::getInstance();
        $db->beginTransaction();

        if ($cliente->inserisci($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaCliente::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->start();
    }

}