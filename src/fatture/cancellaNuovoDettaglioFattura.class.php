<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioFattura.class.php';

class CancellaNuovoDettaglioFattura extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_DETTAGLIO_FATTURA]))
            $_SESSION[self::CANCELLA_DETTAGLIO_FATTURA] = serialize(new CancellaNuovoDettaglioFattura());
        return unserialize($_SESSION[self::CANCELLA_DETTAGLIO_FATTURA]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $dettaglioFattura->cancella($db);
        echo $this->makeTabellaDettagliFattura($dettaglioFattura);
    }

}

?>