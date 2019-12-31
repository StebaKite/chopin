<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class AggiornaDataScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIORNA_DATA_SCADENZA_FORNITORE) === null) {
            parent::setIndexSession(self::AGGIORNA_DATA_SCADENZA_FORNITORE, serialize(new AggiornaDataScadenzaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::AGGIORNA_DATA_SCADENZA_FORNITORE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();

        $scadenzaFornitore->aggiornaData($db);
        echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore, $dettaglioRegistrazione);
    }

}
