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

class AnnullaNuovoCorrispettivoMercato extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::ANNULLA_NUOVO_CORRISPETTIVO_MERCATO])) {
            $_SESSION[self::ANNULLA_NUOVO_CORRISPETTIVO_MERCATO] = serialize(new AnnullaNuovoCorrispettivoMercato());
        }
        return unserialize($_SESSION[self::ANNULLA_NUOVO_CORRISPETTIVO_MERCATO]);
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