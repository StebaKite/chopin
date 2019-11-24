<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'sottoconto.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiungiNuovoDettaglioContoFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_NUOVO_DETTAGLIO_CONTO_FORNITORE])) {
            $_SESSION[self::AGGIUNGI_NUOVO_DETTAGLIO_CONTO_FORNITORE] = serialize(new AggiungiNuovoDettaglioContoFornitore());
        }
        return unserialize($_SESSION[self::AGGIUNGI_NUOVO_DETTAGLIO_CONTO_FORNITORE]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $fornitore = Fornitore::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();

        // Se non esistono fatture da pagare per il fornitore non aggiungo il conto fornitore
        
        if (strpos($dettaglioRegistrazione->getIdTablePagina(), "dettagli_pag") !== false) {
            if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {
                // Se sono già presenti dettagli non aggiungo il conto fornitore
                if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() == 0) {
                    echo $this->aggiungiDettaglio($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $sottoconto);
                } else {
                    echo self::EMPTYSTRING;
                }
            } else {
                echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);
            }            
        } else {
            // Se sono già presenti dettagli non aggiungo il conto fornitore
            if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() == 0) {
                echo $this->aggiungiDettaglio($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $sottoconto);
            } else {
                echo self::EMPTYSTRING;
            }            
        }
    }

    public function aggiungiDettaglio($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $sottoconto) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $db = Database::getInstance();

        $dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
        $dettaglioRegistrazione->setIdRegistrazione(0);
        $dettaglioRegistrazione->setImpRegistrazione(0);

        if ($registrazione->getCodCausale() === $array["pagamentoFornitori"]) {
            $dettaglioRegistrazione->setIndDareavere("D");
        } else {
            $dettaglioRegistrazione->setIndDareavere("A");
        }

        // cerco il fornitore selezionato usando la sua descrizione
        $fornitore->setidFornitore($registrazione->getIdFornitore());
        $fornitore->leggi($db);

        if ($fornitore->getCodFornitore() != "") {
            // prelevo i codici dei conti fornitori in configurazione
            $contoFornitori = explode(",", $array["contiFornitore"]);
            $sottoconto->setCodConto($contoFornitori[0]); // fornitori nazionali
            // cerco il sottoconto corrispondene al fornitore
            $sottoconto->leggi($db);
            $sottoconto->searchSottoconto($fornitore->getCodFornitore());

            // compongo la colonna "conto" da inserire nel dettaglio
            $dettaglioRegistrazione->setCodConto($contoFornitori[0] . "." . $fornitore->getCodFornitore() . " - " . $sottoconto->getDesSottoconto());
            $dettaglioRegistrazione->setCodContoComposto($contoFornitori[0] . "." . $fornitore->getCodFornitore() . " - " . $sottoconto->getDesSottoconto());
            $dettaglioRegistrazione->setCodSottoconto($sottoconto->getCodSottoconto());
            $dettaglioRegistrazione->setIndContoPrincipale("Y");
            $dettaglioRegistrazione->aggiungi();
        }
        return $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);         
    }
}
