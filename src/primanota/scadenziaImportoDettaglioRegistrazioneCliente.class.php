<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class ScadenziaImportoDettaglioRegistrazioneCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::SCADENZIA_IMPORTO_DETTAGLIO_REGISTRAZIONE_CLIENTE) === NULL) {
            parent::setIndexSession(self::SCADENZIA_IMPORTO_DETTAGLIO_REGISTRAZIONE_CLIENTE, serialize(new ScadenziaImportoDettaglioRegistrazioneCliente()));
        }
        return unserialize(parent::getIndexSession(self::SCADENZIA_IMPORTO_DETTAGLIO_REGISTRAZIONE_CLIENTE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente->ripartisciImporto();

        echo $this->makeTabellaScadenzeCliente($scadenzaCliente, $dettaglioRegistrazione);
    }

}