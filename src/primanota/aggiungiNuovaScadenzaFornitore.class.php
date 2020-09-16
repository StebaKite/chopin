<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'registrazione.class.php';

class AggiungiNuovaScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIUNGI_SCADENZA_FORNITORE) === NULL) {
            parent::setIndexSession(self::AGGIUNGI_SCADENZA_FORNITORE, serialize(new AggiungiNuovaScadenzaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::AGGIUNGI_SCADENZA_FORNITORE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $registrazione = Registrazione::getInstance();
        $dettagliRegistrazione = DettaglioRegistrazione::getInstance();
        $fornitore = Fornitore::getInstance();

        $fornitore->setIdFornitore($registrazione->getIdFornitore());
        $fornitore->leggi($db);

        $scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
        $scadenzaFornitore->setTipAddebito($fornitore->getTipAddebito());
        $scadenzaFornitore->setNumFattura($registrazione->getNumFattura());
        $scadenzaFornitore->setNotaScadenza($registrazione->getDesRegistrazione());
        $scadenzaFornitore->aggiungi();

        echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore, $dettagliRegistrazione);
    }

}