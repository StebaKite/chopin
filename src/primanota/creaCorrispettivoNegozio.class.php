<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'lavoroPianificato.class.php';

class CreaCorrispettivoNegozio extends primanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CREA_CORRISPETTIVO_NEGOZIO])) {
            $_SESSION[self::CREA_CORRISPETTIVO_NEGOZIO] = serialize(new CreaCorrispettivoNegozio());
        }
        return unserialize($_SESSION[self::CREA_CORRISPETTIVO_NEGOZIO]);
    }

    public function start() {
        $registragione = Registrazione::getInstance();
        $registragione->prepara();

        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->prepara();
        $dettaglioRegistrazione->setIdTablePagina("dettagli_corneg_cre");

        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
        echo "Ok";
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();

        $array = $utility->getConfig();
        $registrazione->setCodCausale($array["corrispettiviNegozio"]);        

        $this->creaCorrispettivo($utility, $registrazione, $dettaglioRegistrazione);

        $_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
        $controller = unserialize($_SESSION["Obj_primanotacontroller"]);
        $controller->start();
    }

}