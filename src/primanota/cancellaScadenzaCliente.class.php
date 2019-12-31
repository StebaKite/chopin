<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class CancellaScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_SCADENZA_CLIENTE) === NULL) {
            parent::setIndexSession(self::CANCELLA_SCADENZA_CLIENTE, serialize(new CancellaScadenzaCliente()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_SCADENZA_CLIENTE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente->cancella($db);
        echo $this->makeTabellaScadenzeCliente($scadenzaCliente,$dettaglioRegistrazione);
    }

}