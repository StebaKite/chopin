<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'configuraCausale.class.php';

class EscludiContoCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::ESCLUDI_CONTO_CAUSALE])) $_SESSION[self::ESCLUDI_CONTO_CAUSALE] = serialize(new EscludiContoCausale());
		return unserialize($_SESSION[self::ESCLUDI_CONTO_CAUSALE]);
	}

	public function start()
	{
		$configurazioneCausale = ConfigurazioneCausale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$configurazioneCausale->cancellaConto($db);

		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(ConfiguraCausale::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function go() {}
}

?>