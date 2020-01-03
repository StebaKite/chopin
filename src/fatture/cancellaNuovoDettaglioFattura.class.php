<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioFattura.class.php';

class CancellaNuovoDettaglioFattura extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CANCELLA_DETTAGLIO_FATTURA) === NULL) {
            parent::setIndexSession(self::CANCELLA_DETTAGLIO_FATTURA, serialize(new CancellaNuovoDettaglioFattura()));
        }
        return unserialize(parent::getIndexSession(self::CANCELLA_DETTAGLIO_FATTURA));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $dettaglioFattura->cancella($db);
        echo $this->makeTabellaDettagliFattura($dettaglioFattura);
    }

}

?>