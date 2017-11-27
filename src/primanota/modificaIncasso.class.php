<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'primanota.controller.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';
require_once 'lavoroPianificato.class.php';

class ModificaIncasso extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
    function __construct()
    {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }
    
    public function getInstance()
    {
        if (!isset($_SESSION[self::MODIFICA_INCASSO])) $_SESSION[self::MODIFICA_INCASSO] = serialize(new ModificaIncasso());
        return unserialize($_SESSION[self::MODIFICA_INCASSO]);
    }

	public function start()
	{
	    $datiPagina = "";
	    $registrazione = Registrazione::getInstance();
	    $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
	    $cliente = Cliente::getInstance();
	    $scadenzaCliente = ScadenzaCliente::getInstance();
	    $causale = Causale::getInstance();
	    
	    $utility = Utility::getInstance();
	    $db = Database::getInstance();
	    
	    $registrazione->leggi($db);
	    $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
	    
	    $cliente->setIdCliente($registrazione->getIdCliente());
	    $cliente->leggi($db);
	    $scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
	    
	    $scadenzaCliente->setIdCliente($cliente->getIdCliente());
	    $scadenzaCliente->trovaScadenzeDaIncassare($db);
	    $registrazione->setNumFattureDaIncassare($this->makeTabellaFattureDaIncassare($scadenzaCliente,"scadenze_aperte_inc_mod"));
	    
	    $scadenzaCliente->trovaScadenzeIncassate($db);
	    $registrazione->setNumFattureIncassate($this->makeTabellaFattureIncassate($scadenzaCliente,"scadenze_chiuse_inc_mod"));
	    
	    $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
	    $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
	    $dettaglioRegistrazione->setCampoMsgControlloPagina("tddettagli_inc_mod");
	    $dettaglioRegistrazione->setIdTablePagina("dettagli_inc_mod");
	    $dettaglioRegistrazione->setMsgControlloPagina("messaggioControlloDettagliIncasso_mod");
	    $dettaglioRegistrazione->setNomeCampo("descreg_inc_mod");
	    $dettaglioRegistrazione->setLabelNomeCampo("descreg_inc_mod_label");
	    $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
	    
	    $causale->setCodCausale($registrazione->getCodCausale());
	    $causale->loadContiConfigurati($db);
	    
	    $datiPagina = trim($registrazione->getDatRegistrazione()) . "|"
	        . trim($registrazione->getDesRegistrazione()) . "|"
            . trim($registrazione->getCodCausale()) . "|"
            . trim($registrazione->getCodNegozio()) . "|"
            . trim($cliente->getDesCliente()) . "|"
            . trim($registrazione->getNumFattureDaIncassare()) . "|"
            . trim($registrazione->getNumFattureIncassate()) . "|"
            . trim($this->makeTabellaDettagliRegistrazione($dettaglioRegistrazione)) . "|"
            . trim($causale->getContiCausale())
            ;
	                                    
        echo $datiPagina;
	}

	
	public function go()
	{
	    $registrazione = Registrazione::getInstance();
	    $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
	    $cliente = Cliente::getInstance();
	    $scadenzaCliente = ScadenzaCliente::getInstance();
	    $causale = Causale::getInstance();
	    
	    $utility = Utility::getInstance();
	    $db = Database::getInstance();
	    
	    if ($this->aggiornaIncasso($utility, $registrazione, $dettaglioRegistrazione, $scadenzaCliente, $cliente))
	        $_SESSION[self::MSG_DA_MODIFICA] = self::MODIFICA_INCASSO_OK;
	        else $_SESSION[self::MSG_DA_MODIFICA] = self::ERRORE_MODIFICA_REGISTRAZIONE;
	        
	        $_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
	        $controller = unserialize($_SESSION["Obj_primanotacontroller"]);
	        $controller->start();
	}
	
	public function aggiornaIncasso($utility, $registrazione, $dettaglioRegistrazione, $scadenzaCliente, $cliente)
	{
	    $db = Database::getInstance();
	    $db->beginTransaction();
	    $array = $utility->getConfig();
	    
	    if ($registrazione->aggiorna($db))
	    {
	        if ($this->aggiornaDettagli($db,$utility,$registrazione,$dettaglioRegistrazione))
	        {
	            $this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
	            $db->commitTransaction();
	            return true;
	            
	        }
	        else {
	            $db->rollbackTransaction();
	            return false;
	        }
	    }
	    else {
	        $db->rollbackTransaction();
	        return false;
	    }
	}
}

?>