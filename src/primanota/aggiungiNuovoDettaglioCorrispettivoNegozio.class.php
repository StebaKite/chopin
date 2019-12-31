<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'sottoconto.class.php';
require_once 'registrazione.class.php';

class AggiungiNuovoDettaglioCorrispettivoNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getInfoFromServer(self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO) === NULL) {
            parent::setIndexSession(self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO, serialize(new AggiungiNuovoDettaglioCorrispettivoNegozio()));
        }
        return unserialize(parent::getIndexSession(self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $registrazione = Registrazione::getInstance();

        $dettaglioRegistrazione = $this->aggiungiDettagliCorrispettivoNegozio($db, $utility, $array);
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));
        echo $this->makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione);
    }
}