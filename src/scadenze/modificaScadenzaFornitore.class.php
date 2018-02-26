<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'scadenze.controller.class.php';
require_once 'ricercaScadenzeFornitore.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'registrazione.class.php';

class ModificaScadenzaFornitore extends ScadenzeAbstract implements ScadenzeBusinessInterface {
	
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_SCADENZA_FORNITORE])) $_SESSION[self::MODIFICA_SCADENZA_FORNITORE] = serialize(new ModificaScadenzaFornitore());
		return unserialize($_SESSION[self::MODIFICA_SCADENZA_FORNITORE]);
	}
	
	public function start()
	{
		$scadenza = ScadenzaFornitore::getInstance();
		$registrazione = Registrazione::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$db = Database::getInstance();
		
		$scadenza->leggi($db);
		
		$registrazione->setIdRegistrazione($scadenza->getIdRegistrazione());
		$registrazione->leggi($db);
		$registrazioneOriginante = $this->makeTabellaRegistrazioneOriginale($registrazione);
		
		if (trim($scadenza->getStaScadenza()) == self::SCADENZA_CHIUSA) {
			$registrazione->setIdRegistrazione($scadenza->getIdPagamento());
			$registrazione->leggi($db);
			$pagamento = $this->makeTabellaPagamento($registrazione);
		}
		else $pagamento =
		"<tbody>" .
		"	<tr><td class='bg-warning'>Questa scadenza non ha ancora un pagamento associato</td></tr>" .
		"</tbody>";
		
		
		$risultato_xml = $this->root . $array['template'] . self::XML_SCADENZA_FORNITORE;
		
		$replace = array(
				'%data%' => trim($scadenza->getDatScadenza()),
				'%importo%' => trim($scadenza->getImpInScadenza()),
				'%addebito%' => trim($scadenza->getTipAddebito()),
				'%stato%' => trim($scadenza->getStaScadenza()),
				'%fattura%' => trim($scadenza->getNumFattura()),
				'%nota%' => trim($scadenza->getNotaScadenza()),
				'%registrazioneoriginante%' => $registrazioneOriginante,
				'%pagamento%' => $pagamento
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}
	
	public function go()
	{
		$scadenza = ScadenzaFornitore::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$scadenza->aggiorna($db);
		
		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}
}

?>