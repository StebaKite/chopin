<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
//require_once 'creaFornitore.template.php';
require_once 'ricercaFornitore.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class CreaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
		if (!isset($_SESSION[self::CREA_FORNITORE])) $_SESSION[self::CREA_FORNITORE] = serialize(new CreaFornitore());
		return unserialize($_SESSION[self::CREA_FORNITORE]);
	}

	// ------------------------------------------------

	public function start()
	{
 		$fornitore = Fornitore::getInstance();
 		$fornitore->prepara();
 		echo $fornitore->getCodFornitore();
	}

	public function go()
	{
		$fornitore = Fornitore::getInstance();
		
		$db = Database::getInstance();
		$db->beginTransaction();
		
		if ($fornitore->inserisci($db)) $db->commitTransaction();
		else $db->rollbackTransaction();
		
		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaFornitore::getInstance()));
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->start();
	}
}

?>
