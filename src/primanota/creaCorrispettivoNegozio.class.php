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

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_CORRISPETTIVO_NEGOZIO) === NULL) {
            parent::setIndexSession(self::CREA_CORRISPETTIVO_NEGOZIO, serialize(new CreaCorrispettivoNegozio()));
        }
        return unserialize(parent::getIndexSession(self::CREA_CORRISPETTIVO_NEGOZIO));
    }

    public function start() {
        $registragione = Registrazione::getInstance();
        $registragione->prepara();

        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->prepara();
        $dettaglioRegistrazione->setIdTablePagina("dettagli_corneg_cre");

        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));
        echo "Ok";
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();

        $array = $utility->getConfig();
        $registrazione->setCodCausale($array["corrispettiviNegozio"]);        

        $this->creaCorrispettivo($utility, $registrazione, $dettaglioRegistrazione);

        parent::setIndexSession("Obj_primanotacontroller", serialize(new PrimanotaController(RicercaRegistrazione::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_primanotacontroller"));
        $controller->start();
    }

}