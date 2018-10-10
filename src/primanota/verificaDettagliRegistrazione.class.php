<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';

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
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        if ($dettaglioRegistrazione->verificaQuadratura()) {

            // Fornitore
            
            if (parent::isNotEmpty($registrazione->getIdFornitore())) {                
                $scadenzaFornitore = ScadenzaFornitore::getInstance();
                $importoTotaleScadenze = $scadenzaFornitore->getSommaImportiScadenze();                
                $importoContoFornitore = $dettaglioRegistrazione->getImportoContoPrincipale();
                
                if ($importoTotaleScadenze != $importoContoFornitore) {
                    echo "Errore scadenze";
                }
            }
            
            // Cliente
            
            
            
            
            
            
            
            echo "";
        } else             
            echo "Errore dettagli";
    }

    public function go() {
        $this->start();
    }

}
