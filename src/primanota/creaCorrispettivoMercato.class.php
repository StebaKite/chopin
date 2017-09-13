<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'lavoroPianificato.class.php';

class CreaCorrispettivoMercato extends primanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_CORRISPETTIVO_MERCATO])) $_SESSION[self::CREA_CORRISPETTIVO_MERCATO] = serialize(new CreaCorrispettivoMercato());
		return unserialize($_SESSION[self::CREA_CORRISPETTIVO_MERCATO]);
	}

	public function start()
	{
		$this->go();
	}

	public function go()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$utility = Utility::getInstance();

		if ($this->creaCorrispettivo($utility, $registrazione, $dettaglioRegistrazione))
			$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_REGISTRAZIONE_OK;
		else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_REGISTRAZIONE;

		$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
		$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
		$controller->start();
	}
}

?>