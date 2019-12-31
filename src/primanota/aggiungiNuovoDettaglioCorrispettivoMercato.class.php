<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'sottoconto.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiungiNuovoDettaglioCorrispettivoMercato extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO) === NULL) {
            parent::setIndexSession(self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO, serialize(new AggiungiNuovoDettaglioCorrispettivoMercato()));
        }
        return unserialize(parent::getIndexSession(self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();

        $dettaglioRegistrazione->aggiungiDettagliCorrispettivoMercato($db);
        echo $this->makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione);
    }
}