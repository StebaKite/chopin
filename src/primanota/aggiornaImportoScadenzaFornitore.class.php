<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class AggiornaImportoScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE])) {
            $_SESSION[self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE] = serialize(new AggiornaImportoScadenzaFornitore());
        }
        return unserialize($_SESSION[self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $dettagliRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore->aggiornaImporto($db);
        echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore, $dettagliRegistrazione);
    }

}
