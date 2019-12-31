<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class ScadenziaImportoDettaglioRegistrazioneFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::SCADENZIA_IMPORTO_DETTAGLIO_REGISTRAZIONE_FORNITORE) === NULL) {
            parent::setIndexSession(self::SCADENZIA_IMPORTO_DETTAGLIO_REGISTRAZIONE_FORNITORE, serialize(new ScadenziaImportoDettaglioRegistrazioneFornitore()));
        }
        return unserialize(parent::getIndexSession(self::SCADENZIA_IMPORTO_DETTAGLIO_REGISTRAZIONE_FORNITORE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $dettagliRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore->ripartisciImporto();
        
        echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore,$dettagliRegistrazione);
    }
}