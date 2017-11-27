<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';
require_once 'scadenzaFornitore.class.php';

class AggiungiFatturaPagata extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
    function __construct() {
        
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }
    
    public function getInstance()
    {
        if (!isset($_SESSION[self::AGGIUNGI_FATTURA_PAGATA])) $_SESSION[self::AGGIUNGI_FATTURA_PAGATA] = serialize(new AggiungiFatturaPagata());
        return unserialize($_SESSION[self::AGGIUNGI_FATTURA_PAGATA]);
    }
    public function start() {
        $this->go();
    }
    
    public function go()
    {
        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $fornitore = Fornitore::getInstance();
        
        $fornitore->setIdFornitore($registrazione->getIdFornitore());
        
        if ($scadenzaFornitore->getIdTableScadenzeAperte() == "scadenze_aperte_pag_cre")
        {
            $scadenzaFornitore->leggi($db);
            $scadenzaFornitore->aggiungiScadenzaPagata();
            
            echo $this->makeTabellaFatturePagate($scadenzaFornitore) . "|" . $this->refreshTabellaFattureDaPagare($scadenzaFornitore);
        }
        elseif ($scadenzaFornitore->getIdTableScadenzeAperte() == "scadenze_aperte_pag_mod")
        {
            $scadenzaFornitore->setStaScadenza("10");   // pagata e chiusa
            $scadenzaFornitore->setIdPagamento($registrazione->getIdRegistrazione());
            $scadenzaFornitore->cambiaStatoScadenza($db);   
            $scadenzaFornitore->trovaScadenzeDaPagare($db);
            $scadenzaFornitore->trovaScadenzePagate($db);
            
            echo $this->makeTabellaFatturePagate($scadenzaFornitore) . "|" . $this->makeTabellaFattureDaPagare($scadenzaFornitore);            
        }
    }
}

