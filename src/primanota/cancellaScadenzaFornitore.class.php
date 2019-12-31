<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class CancellaScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_SCADENZA_FORNITORE) === NULL) {
            parent::setIndexSession(self::CANCELLA_SCADENZA_FORNITORE, serialize(new CancellaScadenzaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_SCADENZA_FORNITORE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $dettagliRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore->cancella($db);
        echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore,$dettagliRegistrazione);
    }
}