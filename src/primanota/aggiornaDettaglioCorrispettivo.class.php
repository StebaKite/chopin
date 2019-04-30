<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiornaDettaglioCorrispettivo extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIORNA_DETTAGLIO_CORRISPETTIVO]))
            $_SESSION[self::AGGIORNA_DETTAGLIO_CORRISPETTIVO] = serialize(new AggiornaDettaglioCorrispettivo());
        return unserialize($_SESSION[self::AGGIORNA_DETTAGLIO_CORRISPETTIVO]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->aggiornaDettaglio();        
        $registrazione = Registrazione::getInstance();        
        echo $this->makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione);
    }
}