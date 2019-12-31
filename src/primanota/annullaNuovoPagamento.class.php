<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'sottoconto.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'scadenzaFornitore.class.php';

class AnnullaNuovoPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::ANNULLA_NUOVO_PAGAMENTO) === NULL) {
            parent::setIndexSession(self::ANNULLA_NUOVO_PAGAMENTO, serialize(new AnnullaNuovoPagamento()));
        }
        return unserialize(parent::getIndexSession(self::ANNULLA_NUOVO_PAGAMENTO));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $registrazione->preparaFiltri();
        $dettaglioRegistrazione->prepara();
        $scadenzaFornitore->prepara();

        echo "Okay";
    }

}