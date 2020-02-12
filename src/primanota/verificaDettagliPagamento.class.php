<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaFornitore.class.php';

class VerificaDettagliPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VERIFICA_DETTAGLI_PAGAMENTO) === NULL) {
            parent::setIndexSession(self::VERIFICA_DETTAGLI_PAGAMENTO, serialize(new VerificaDettagliPagamento()));
        }
        return unserialize(parent::getIndexSession(self::VERIFICA_DETTAGLI_PAGAMENTO));
    }

    public function start() {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        if ($dettaglioRegistrazione->verificaQuadratura()) {

            $scadenzaFornitore = ScadenzaFornitore::getInstance();
            $importoTotaleScadenzePagate = floatval($scadenzaFornitore->getSommaImportiScadenzePagate());
            $importoContoFornitore = $dettaglioRegistrazione->getImportoContoPrincipale();

            if ($importoTotaleScadenzePagate === 0) {
                 echo "Errore scadenze";               
            } else {
                if (bccomp($importoTotaleScadenzePagate, $importoContoFornitore) === 0) {
                    echo "";
                } else {
                    echo "Errore scadenze";
                }
            }
        } else {
            echo "Errore dettagli";
        }
    }

    public function go() {
        $this->start();
    }

}