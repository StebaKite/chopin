<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configurazioni.controller.class.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class CancellaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
{

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
		if (!isset($_SESSION[self::CANCELLA_CAUSALE])) $_SESSION[self::CANCELLA_CAUSALE] = serialize(new CancellaCausale());
		return unserialize($_SESSION[self::CANCELLA_CAUSALE]);
	}

	public function start()
	{
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$causale->cancella($db);

		$_SESSION[self::MSG_DA_CANCELLAZIONE] = self::CANCELLA_CAUSALE_OK;
		
		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}
	
	public function go() 
	{
		$this->start();
	}
}

?>