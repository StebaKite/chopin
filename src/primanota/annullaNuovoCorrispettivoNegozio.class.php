<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'cliente.class.php';
require_once 'sottoconto.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'scadenzaFornitore.class.php';

class AnnullaNuovoCorrispettivoNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::ANNULLA_NUOVO_CORRISPETTIVO_NEGOZIO) === null) {
            parent::setIndexSession(self::ANNULLA_NUOVO_CORRISPETTIVO_NEGOZIO, serialize(new AnnullaNuovoCorrispettivoNegozio()));
        }
        return unserialize(parent::getIndexSession(self::ANNULLA_NUOVO_CORRISPETTIVO_NEGOZIO));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $registrazione->preparaFiltri();
        $dettaglioRegistrazione->prepara();

        echo "Okay";
    }

}