<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class ModificaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_CAUSALE])) $_SESSION[self::MODIFICA_CAUSALE] = serialize(new ModificaCausale());
		return unserialize($_SESSION[self::MODIFICA_CAUSALE]);
	}

	public function start()
	{
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$causale->leggi($db);
		$_SESSION[self::CAUSALE] = serialize($causale);

		$datiPagina =
		trim($causale->getCodCausale()) . "|" .
		trim($causale->getDesCausale()) . "|" .
		trim($causale->getCatCausale());

		echo $datiPagina;
	}

	public function go()
	{
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();

		if ($this->controlliLogici($causale)) {

			if ($causale->aggiorna($db)) {
				$_SESSION[self::MSG_DA_MODIFICA_CAUSALE] = self::AGGIORNA_CAUSALE_OK;
			}
			else {
				$_SESSION[self::MSG_DA_MODIFICA_CAUSALE] = $_SESSION[self::MESSAGGIO];
			}
		}
		else {
			$_SESSION[self::MSG_DA_MODIFICA_CAUSALE] = $_SESSION[self::MESSAGGIO];
		}

		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function controlliLogici($causale)
	{
		$esito = TRUE;
		$msg = "<br>";

		if ($causale->getDesCausale() == "") {
			$msg .= self::ERRORE_DESCRIZIONE_CAUSALE;
			$esito = FALSE;
		}

		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}
		return $esito;
	}
}

?>