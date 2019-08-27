<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiornaDettaglioRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIORNA_DETTAGLIO_REGISTRAZIONE])) {
            $_SESSION[self::AGGIORNA_DETTAGLIO_REGISTRAZIONE] = serialize(new AggiornaDettaglioRegistrazione());
        }
        return unserialize($_SESSION[self::AGGIORNA_DETTAGLIO_REGISTRAZIONE]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->aggiornaDettaglio();
        $registrazione = Registrazione::getInstance();
        echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione);
    }

}
