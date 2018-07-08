<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioFattura.class.php';

class AggiungiNuovoDettaglioFattura extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_DETTAGLIO_FATTURA]))
            $_SESSION[self::AGGIUNGI_DETTAGLIO_FATTURA] = serialize(new AggiungiNuovoDettaglioFattura());
        return unserialize($_SESSION[self::AGGIUNGI_DETTAGLIO_FATTURA]);
    }

    public function start() {
        $this->go();
    }

    public function go() {

        $dettaglioFattura = DettaglioFattura::getInstance();
        $dettaglioFattura->aggiungi();
        echo $this->makeTabellaDettagliFattura($dettaglioFattura);
    }

}

?>