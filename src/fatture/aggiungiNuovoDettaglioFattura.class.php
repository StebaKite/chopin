<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioFattura.class.php';

class AggiungiNuovoDettaglioFattura extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIUNGI_DETTAGLIO_FATTURA) === NULL) {
            parent::setIndexSession(self::AGGIUNGI_DETTAGLIO_FATTURA, serialize(new AggiungiNuovoDettaglioFattura()));
        }
        return unserialize(parent::getIndexSession(self::AGGIUNGI_DETTAGLIO_FATTURA));
    }

    public function start() {
        $this->go();
    }

    public function go() {

        $dettaglioFattura = DettaglioFattura::getInstance();
        $dettaglioFattura->aggiungi();
        echo $this->makeTabellaDettagliFattura($dettaglioFattura);
    }

}

?>