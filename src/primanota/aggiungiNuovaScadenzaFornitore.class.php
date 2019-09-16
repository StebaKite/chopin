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

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_SCADENZA_FORNITORE])) {
            $_SESSION[self::AGGIUNGI_SCADENZA_FORNITORE] = serialize(new AggiungiNuovaScadenzaFornitore());
        }
        return unserialize($_SESSION[self::AGGIUNGI_SCADENZA_FORNITORE]);
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
        $scadenzaFornitore->aggiungi();

        echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore, $dettagliRegistrazione);
    }

}