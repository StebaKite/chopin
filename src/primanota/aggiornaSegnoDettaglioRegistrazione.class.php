<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'registrazione.class.php';

class AggiornaSegnoDettaglioRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIORNA_SEGNO_DETTAGLIO_REGISTRAZIONE) === null) {
            parent::setIndexSession(self::AGGIORNA_SEGNO_DETTAGLIO_REGISTRAZIONE, serialize(new AggiornaSegnoDettaglioRegistrazione()));
        }
        return unserialize(parent::getIndexSession(self::AGGIORNA_SEGNO_DETTAGLIO_REGISTRAZIONE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->aggiornaSegno($db);
        $registrazione = Registrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();

        echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);
    }

}
