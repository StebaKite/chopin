<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaFornitore.class.php';

class VerificaDettagliPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::VERIFICA_DETTAGLI_PAGAMENTO])) {
            $_SESSION[self::VERIFICA_DETTAGLI_PAGAMENTO] = serialize(new VerificaDettagliPagamento());
        }
        return unserialize($_SESSION[self::VERIFICA_DETTAGLI_PAGAMENTO]);
    }

    public function start() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        if ($dettaglioRegistrazione->verificaQuadratura()) {

            $scadenzaFornitore = ScadenzaFornitore::getInstance();
            $importoTotaleScadenzePagate = $scadenzaFornitore->getSommaImportiScadenzePagate();                
            $importoContoFornitore = $dettaglioRegistrazione->getImportoContoPrincipale();

            if (($importoTotaleScadenzePagate > 0) && ($importoTotaleScadenzePagate != $importoContoFornitore)) {
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
