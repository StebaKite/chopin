<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaCliente.class.php';

class AggiungiNuovaScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_SCADENZA_CLIENTE]))
            $_SESSION[self::AGGIUNGI_SCADENZA_CLIENTE] = serialize(new AggiungiNuovaScadenzaCliente());
        return unserialize($_SESSION[self::AGGIUNGI_SCADENZA_CLIENTE]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $cliente = Cliente::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $registrazione = Registrazione::getInstance();

        $cliente->setIdCliente($registrazione->getIdCliente());
        $cliente->leggi($db);

        $scadenzaCliente->setIdCliente($cliente->getIdCliente());
        $scadenzaCliente->setTipAddebito($cliente->getTipAddebito());
        $scadenzaCliente->setNumFattura($registrazione->getNumFattura());
        $scadenzaCliente->aggiungi();
        
        echo $this->makeTabellaScadenzeCliente($scadenzaCliente);
    }

}

?>