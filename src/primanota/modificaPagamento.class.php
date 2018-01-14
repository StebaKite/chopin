<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'causale.class.php';

class ModificaPagamento extends primanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
	    $this->root = $_SERVER['DOCUMENT_ROOT'];
	    $this->utility = Utility::getInstance();
	    $this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
	    if (!isset($_SESSION[self::MODIFICA_PAGAMENTO])) $_SESSION[self::MODIFICA_PAGAMENTO] = serialize(new ModificaPagamento());
	    return unserialize($_SESSION[self::MODIFICA_PAGAMENTO]);
	}
	
	public function start()
	{
	    $datiPagina = "";
	    $registrazione = Registrazione::getInstance();
	    $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
	    $fornitore = Fornitore::getInstance();
	    $scadenzaFornitore = ScadenzaFornitore::getInstance();
	    $causale = Causale::getInstance();
	    
	    $utility = Utility::getInstance();
	    $db = Database::getInstance();
	    
	    $registrazione->leggi($db);
	    $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
	    
        $fornitore->setIdFornitore($registrazione->getIdFornitore());
        $fornitore->leggi($db);
        $scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
        
        $scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());        
        $scadenzaFornitore->trovaScadenzeDaPagare($db);
        $registrazione->setNumFattureDaPagare($this->makeTabellaFattureDaPagare($scadenzaFornitore,"scadenze_aperte_pag_mod"));

        $scadenzaFornitore->trovaScadenzePagate($db);
        $registrazione->setNumFatturePagate($this->makeTabellaFatturePagate($scadenzaFornitore,"scadenze_chiuse_pag_mod"));
                
	    $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
	    $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
	    $dettaglioRegistrazione->setCampoMsgControlloPagina("tddettagli_pag_mod");
	    $dettaglioRegistrazione->setIdTablePagina("dettagli_pag_mod");
	    $dettaglioRegistrazione->setMsgControlloPagina("messaggioControlloDettagliPagamento_mod");
	    $dettaglioRegistrazione->setNomeCampo("descreg_pag_mod");
	    $dettaglioRegistrazione->setLabelNomeCampo("descreg_pag_mod_label");
	    $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
	    
	    $causale->setCodCausale($registrazione->getCodCausale());
	    $causale->loadContiConfigurati($db);
	    
	    $risultato_xml = $this->root . $array['template'] . self::XML_MODIFICA_PAGAMENTO;
	    
	    $replace = array(
	    		'%datareg%' => trim($registrazione->getDatRegistrazione()),
	    		'%descreg%' => trim($registrazione->getDesRegistrazione()),
	    		'%causale%' => trim($registrazione->getCodCausale()),
	    		'%codneg%' => trim($registrazione->getCodNegozio()),
	    		'%fornitore%' => trim($fornitore->getDesFornitore()),
	    		'%scadenzepagate%' => trim($this->makeTabellaFatturePagate($scadenzaFornitore)),
	    		'%scadenzedapagare%' => trim($this->makeTabellaFattureDaPagare($scadenzaFornitore)),
	    		'%dettagli%' => trim($this->makeTabellaDettagliRegistrazione($dettaglioRegistrazione)),
	    		'%conti%' => $causale->getContiCausale()
	    );
	    $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
	    echo $utility->tailTemplate($template);
	}

	public function go()
	{
	    $registrazione = Registrazione::getInstance();
	    $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
	    $fornitore = Fornitore::getInstance();
	    $scadenzaFornitore = ScadenzaFornitore::getInstance();
	    $causale = Causale::getInstance();
	    
	    $utility = Utility::getInstance();
	    $db = Database::getInstance();

	    $this->aggiornaPagamento($utility, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $fornitore);
	        
        $_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
        $controller = unserialize($_SESSION["Obj_primanotacontroller"]);
        $controller->start();
    }

	public function aggiornaPagamento($utility, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $fornitore)
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