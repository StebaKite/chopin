<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class AggiornaDataScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
    function __construct() {
        
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }
    
    public static function getInstance()
    {
        if (!isset($_SESSION[self::AGGIORNA_DATA_SCADENZA_CLIENTE])) $_SESSION[self::AGGIORNA_DATA_SCADENZA_CLIENTE] = serialize(new AggiornaDataScadenzaCliente());
        return unserialize($_SESSION[self::AGGIORNA_DATA_SCADENZA_CLIENTE]);
    }
    
    public function start() {
        $this->go();
    }
    
    public function go()
    {
        $db = Database::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente->aggiornaData($db);
        echo $this->makeTabellaScadenzeCliente($scadenzaCliente,$dettaglioRegistrazione);
    }
}