<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'cliente.class.php';
require_once 'sottoconto.class.php';
require_once 'dettaglioRegistrazione.class.php';

class AggiungiNuovoDettaglioContoCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getInfoFromServer(self::AGGIUNGI_NUOVO_DETTAGLIO_CONTO_CLIENTE) === NULL) {
            parent::setIndexSession(self::AGGIUNGI_NUOVO_DETTAGLIO_CONTO_CLIENTE, serialize(new AggiungiNuovoDettaglioContoCliente()));
        }
        return unserialize(parent::getIndexSession(self::AGGIUNGI_NUOVO_DETTAGLIO_CONTO_CLIENTE));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $cliente = Cliente::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        
        // Se non esistono fatture da incassare per il cliente non aggiungo il conto cliente
        
        if (strpos($dettaglioRegistrazione->getIdTablePagina(), "dettagli_inc") !== false) {
            if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {
                // Se sono già presenti dettagli non aggiungo il conto cliente
                if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() == 0) {
                    echo $this->aggiungiDettaglio($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $cliente, $sottoconto);
                } else {
                    echo self::EMPTYSTRING;
                }
            } else {
                echo $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);
            }
        } else {
            // Se sono già presenti dettagli non aggiungo il conto fornitore
            if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() == 0) {
                echo $this->aggiungiDettaglio($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $cliente, $sottoconto);
            } else {
                echo self::EMPTYSTRING;
            }            
        }
    }
    
    public function aggiungiDettaglio($registrazione, $dettaglioRegistrazione, $scadenzaCliente, $scadenzaCliente, $cliente, $sottoconto) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $db = Database::getInstance();
    
        $dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
        $dettaglioRegistrazione->setIdRegistrazione(0);
        $dettaglioRegistrazione->setImpRegistrazione(0);
        $dettaglioRegistrazione->setIndDareavere("D");

        // cerco il fornitore selezionato usando la sua descrizione
        $cliente->setIdCliente($registrazione->getIdCliente());
        $cliente->leggi($db);

        // prelevo i codici dei conti fornitori in configurazione
        $contoClienti = explode(",", $array["contiCliente"]);
        $sottoconto->setCodConto($contoClienti[0]); // clienti nazionali
        // cerco il sottoconto corrispondene al cliente
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($cliente->getCodCliente());

        // compongo la colonna "conto" da inserire nel dettaglio
        $dettaglioRegistrazione->setCodConto($contoClienti[0] . "." . $cliente->getCodCliente() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodContoComposto($contoClienti[0] . "." . $cliente->getCodCliente() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodSottoconto($sottoconto->getCodSottoconto());
        $dettaglioRegistrazione->setIndContoPrincipale("Y");
        $dettaglioRegistrazione->aggiungi();

        return $this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente);
    }    
}
