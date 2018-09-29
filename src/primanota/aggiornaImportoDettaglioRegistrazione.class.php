<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiornaImportoDettaglioRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::AGGIORNA_IMPORTO_DETTAGLIO_REGISTRAZIONE]))
            $_SESSION[self::AGGIORNA_IMPORTO_DETTAGLIO_REGISTRAZIONE] = serialize(new AggiornaImportoDettaglioRegistrazione());
        return unserialize($_SESSION[self::AGGIORNA_IMPORTO_DETTAGLIO_REGISTRAZIONE]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->aggiornaImporto($db);
        
        echo $this->makeTabellaDettagliRegistrazione($dettaglioRegistrazione);
    }
}

?>