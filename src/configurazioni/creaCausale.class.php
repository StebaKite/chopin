<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'creaCausale.template.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class CreaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
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
		if (!isset($_SESSION[self::CREA_CAUSALE])) $_SESSION[self::CREA_CAUSALE] = serialize(new CreaCausale());
		return unserialize($_SESSION[self::CREA_CAUSALE]);
	}

	public function start()
	{
		$this->go();
	}

	public function go()
	{
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$creaCausaleTemplate = CreaCausaleTemplate::getInstance();

		if ($creaCausaleTemplate->controlliLogici()) {

			if ($this->creaCausale($utility, $causale)) {
				$_SESSION[self::MSG_DA_CREAZIONE_CAUSALE] = self::CREA_CAUSALE_OK;
			}
			else {
				$_SESSION[self::MSG_DA_CREAZIONE_CAUSALE] = self::ERRORE_CREAZIONE_CAUSALE;
			}
		}
		else {
			$_SESSION[self::MSG_DA_CREAZIONE_CAUSALE] = $_SESSION[self::MESSAGGIO];
		}

		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function creaCausale($utility, $causale)
	{
		$db = Database::getInstance();
		$db->beginTransaction();

		if ($causale->inserisci($db)) {

			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		$_SESSION[self::MESSAGGIO] = self::ERRORE_CREAZIONE_CAUSALE;
		return false;
	}

	public function preparaPagina($creaCausaleTemplate)
	{
		$creaCausaleTemplate->setAzione(self::AZIONE_CREA_CAUSALE);
		$creaCausaleTemplate->setConfermaTip("%ml.confermaCreaCausale%");
		$creaCausaleTemplate->setTitoloPagina("%ml.creaNuovaCausale%");
	}
}

?>