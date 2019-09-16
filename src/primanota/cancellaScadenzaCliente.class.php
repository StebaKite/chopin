<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class CancellaScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_SCADENZA_CLIENTE])) {
            $_SESSION[self::CANCELLA_SCADENZA_CLIENTE] = serialize(new CancellaScadenzaCliente());
        }
        return unserialize($_SESSION[self::CANCELLA_SCADENZA_CLIENTE]);
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