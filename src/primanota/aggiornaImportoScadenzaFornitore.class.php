<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class AggiornaImportoScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE) === null) {
            parent::setIndexSession(self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE, serialize(new AggiornaImportoScadenzaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE));
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
