<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class AggiornaImportoScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE) === null) {
            parent::setIndexSession(self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE, serialize(new AggiornaImportoScadenzaCliente()));
        }
        return unserialize(parent::getIndexSession(self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente->aggiornaImporto($db);
        $scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_mod");
        parent::setIndexSession(self::SCADENZA_CLIENTE, serialize($scadenzaCliente));
        echo $this->makeTabellaScadenzeCliente($scadenzaCliente, $dettaglioRegistrazione);
    }

}
