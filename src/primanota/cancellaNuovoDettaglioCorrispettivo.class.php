<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'registrazione.class.php';

class CancellaNuovoDettaglioCorrispettivo extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CANCELLA_DETTAGLIO_CORRISPETTIVO]))
            $_SESSION[self::CANCELLA_DETTAGLIO_CORRISPETTIVO] = serialize(new CancellaNuovoDettaglioCorrispettivo());
        return unserialize($_SESSION[self::CANCELLA_DETTAGLIO_CORRISPETTIVO]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->cancella($db);
        $registrazione = Registrazione::getInstance();

        echo $this->makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione);
    }
}