<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoDettaglioRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_DETTAGLIO_REGISTRAZIONE])) {
            $_SESSION[self::AGGIUNGI_DETTAGLIO_REGISTRAZIONE] = serialize(new AggiungiNuovoDettaglioRegistrazione());
        }
        return unserialize($_SESSION[self::AGGIUNGI_DETTAGLIO_REGISTRAZIONE]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();

        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
        $dettaglioRegistrazione->setIdRegistrazione(0);

        if (parent::isNotEmpty($registrazione->getIdFornitore())) {
            $contoFornitori = explode(",", $array["contiFornitore"]);       // prelevo i codici dei conti fornitori in configurazione
            $sottoconto->setCodConto($contoFornitori[0]);                   // fornitori nazionali        
        } elseif (parent::isNotEmpty($registrazione->getIdCliente())) {
            $contoClienti = explode(",", $array["contiCliente"]);           // prelevo i codici dei conti clienti in configurazione
            $sottoconto->setCodConto($contoClienti[0]);
        }

        // Se nella descrizione del dettaglio da inserire e' presente il codice conto cliente/fornitore significa che è un dettaglio principale

        if (strpos($dettaglioRegistrazione->getCodConto(), $sottoconto->getCodConto()) > -1) {
            $dettaglioRegistrazione->setIndContoPrincipale("Y");
        } else {
            $dettaglioRegistrazione->setIndContoPrincipale("N");
        }

        $dettaglioRegistrazione->aggiungi();
        echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);
    }

}
