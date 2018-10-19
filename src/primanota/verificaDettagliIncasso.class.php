<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaCliente.class.php';

class VerificaDettagliIncasso extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::VERIFICA_DETTAGLI_INCASSO])) {
            $_SESSION[self::VERIFICA_DETTAGLI_INCASSO] = serialize(new VerificaDettagliIncasso());
        }
        return unserialize($_SESSION[self::VERIFICA_DETTAGLI_INCASSO]);
    }

    public function start() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        if ($dettaglioRegistrazione->verificaQuadratura()) {

            $scadenzaCliente = ScadenzaCliente::getInstance();
            $importoTotaleScadenzeIncassate = $scadenzaCliente->getSommaImportiScadenzeIncassate();                
            $importoContoCliente = $dettaglioRegistrazione->getImportoContoPrincipale();

            if (($importoTotaleScadenzeIncassate > 0) && ($importoTotaleScadenzeIncassate != $importoContoCliente)) {
                echo "Errore scadenze";
            }
            echo "";
        } else             
            echo "Errore dettagli";
    }

    public function go() {
        $this->start();
    }

}
