<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'mercato.class.php';
require_once 'ricercaMercato.class.php';
require_once 'anagrafica.controller.class.php';

class CancellaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	function __construct()
	{
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
		if (!isset($_SESSION[self::CANCELLA_MERCATO])) $_SESSION[self::CANCELLA_MERCATO] = serialize(new CancellaMercato());
		return unserialize($_SESSION[self::CANCELLA_MERCATO]);
	}

	public function start() {

		$mercato = Mercato::getInstance();
		$db = Database::getInstance();

		if ($mercato->cancella($db)) {
			$_SESSION["messaggioCancellazione"] = self::CANCELLA_MERCATO_OK;
		}
		else {
			$_SESSION["messaggioCancellazione"] = self::ERRORE_LETTURA;		
		}
		
		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaMercato::getInstance()));
		
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->setRequest("start");
		$controller->start();
	}
	
	public function go() {
		$this->start();
	}
}

?>