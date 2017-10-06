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
        
        $scadenzaFornitore->setIdFornitore($registrazione->getIdFornitore());
        $scadenzaFornitore->setStaScadenza("10");   // pagata e chiusa
        $scadenzaFornitore->setIdPagamento($registrazione->getIdRegistrazione());
        $scadenzaFornitore->cambiaStatoScadenza($db);
        
        $scadenzaFornitore->trovaScadenzeDaPagare($db);
        $scadenzaFornitore->trovaScadenzePagate($db);
        
        echo $this->makeTabellaFatturePagate($scadenzaFornitore,"scadenze_chiuse_pag_mod") . "|" .
             $this->makeTabellaFattureDaPagare($scadenzaFornitore,"scadenze_aperte_pag_mod");        
    }
}

