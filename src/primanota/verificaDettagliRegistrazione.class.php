<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'dettaglioRegistrazione.class.php';

class VerificaDettagliRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::VERIFICA_DETTAGLI_REGISTRAZIONE])) {
            $_SESSION[self::VERIFICA_DETTAGLI_REGISTRAZIONE] = serialize(new VerificaDettagliRegistrazione());
        }
        return unserialize($_SESSION[self::VERIFICA_DETTAGLI_REGISTRAZIONE]);
    }

    public function start() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        if ($dettaglioRegistrazione->verificaQuadratura())
            echo "";
        else
            echo "Errore dettagli";
    }

    public function go() {
        $this->start();
    }

}