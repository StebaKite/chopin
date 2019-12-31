<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';

class VerificaDettagliRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VERIFICA_DETTAGLI_REGISTRAZIONE) === NULL) {
            parent::setIndexSession(self::VERIFICA_DETTAGLI_REGISTRAZIONE, serialize(new VerificaDettagliRegistrazione()));
        }
        return unserialize(parent::getIndexSession(self::VERIFICA_DETTAGLI_REGISTRAZIONE));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        if ($dettaglioRegistrazione->verificaQuadratura()) {

            // Fornitore
            
            if (parent::isNotEmpty($registrazione->getIdFornitore())) {                
                $scadenzaFornitore = ScadenzaFornitore::getInstance();
                
                if ($scadenzaFornitore->getQtaScadenzePagate() > 0) {
                    $importoTotaleScadenze = $scadenzaFornitore->getSommaImportiScadenzePagate();
                }
                else {
                    if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {
                        $importoTotaleScadenze = $scadenzaFornitore->getSommaImportiScadenzeDaPagare();                
                    }
                }
                $importoContoFornitore = $dettaglioRegistrazione->getImportoContoPrincipale();
                
                if (($importoTotaleScadenze > 0) && ($importoTotaleScadenze != $importoContoFornitore)) {
                    echo "Errore scadenze";
                }
            }
            
            // Cliente
            
            elseif (parent::isNotEmpty($registrazione->getIdCliente())) {                
                $scadenzaCliente = ScadenzaCliente::getInstance();
                
                if ($scadenzaCliente->getQtaScadenzeIncassate() > 0) {
                    $importoTotaleScadenze = $scadenzaCliente->getSommaImportiScadenzeIncassate();                
                }
                else {
                    if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {
                        $importoTotaleScadenze = $scadenzaCliente->getSommaImportiScadenzeDaIncassare();                
                    }
                }
                $importoContoCliente = $dettaglioRegistrazione->getImportoContoPrincipale();
                
                if (($importoTotaleScadenze > 0) && ($importoTotaleScadenze != $importoContoCliente)) {
                    echo "Errore scadenze";
                }
            }            
            echo "";        // tutto ok
        } else             
            echo "Errore dettagli";
    }

    public function go() {
        $this->start();
    }

}