<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiornaDettaglioRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIORNA_DETTAGLIO_REGISTRAZIONE) === null) {
            parent::setIndexSession(self::AGGIORNA_DETTAGLIO_REGISTRAZIONE, serialize(new AggiornaDettaglioRegistrazione()));
        }
        return unserialize(parent::getIndexSession(self::AGGIORNA_DETTAGLIO_REGISTRAZIONE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->aggiornaDettaglio();
        $registrazione = Registrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);
    }

}
