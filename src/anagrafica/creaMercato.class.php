<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'mercato.class.php';
require_once 'ricercaMercato.class.php';
require_once 'anagrafica.controller.class.php';

class CreaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
		
		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_MERCATO])) $_SESSION[self::CREA_MERCATO] = serialize(new CreaMercato());
		return unserialize($_SESSION[self::CREA_MERCATO]);
	}

	public function start() {}
	
	public function go() {
		
		$this->creaMercato();			
		
		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaMercato::getInstance()));
		
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->setRequest("start");
		$controller->start();
	}

	private function creaMercato() {

		$db = Database::getInstance();
		$mercato = Mercato::getInstance();
		
		$desMercato = str_replace("'","''",$mercato->getDesMercato());
		$mercato->setDesMercato($desMercato);
		
		$cittamercato = str_replace("'","''",$mercato->getCittaMercato());
		$mercato->setCittaMercato($cittamercato);
		
		if ($mercato->nuovo($db)) {
			$_SESSION["messaggioCreazione"] = self::CREA_MERCATO_OK;
			return TRUE;
		}
		else {
			unset($_SESSION["messaggioCreazione"]);
			return FALSE;				
		}
	}
}	
	
?>