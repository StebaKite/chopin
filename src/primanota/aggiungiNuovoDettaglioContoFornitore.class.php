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
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $fornitore = Fornitore::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();

        // Se sono già presenti dettagli non aggiungo il conto fornitore
        if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() == 0) {
            $dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
            $dettaglioRegistrazione->setIdRegistrazione(0);
            $dettaglioRegistrazione->setImpRegistrazione(0);
            $dettaglioRegistrazione->setIndDareavere("A");

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
                $dettaglioRegistrazione->setCodSottoconto($sottoconto->getCodSottoconto());
                $dettaglioRegistrazione->setIndContoPrincipale("Y");
                $dettaglioRegistrazione->aggiungi();
            }
            echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione);
        } else {
            echo "";
        }
    }

}

?>