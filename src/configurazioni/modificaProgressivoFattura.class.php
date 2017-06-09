<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaProgressivoFattura.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';

class ModificaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA])) $_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA] = serialize(new ModificaProgressivoFattura());
		return unserialize($_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA]);
	}

	public function start()
	{

		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$progressivoFattura->leggi($db);
		$_SESSION[self::PROGRESIVO_FATTURA] = serialize($progressivoFattura);

		$datiPagina =
		$progressivoFattura->getCatCliente() . "|" .
		$progressivoFattura->getNegProgr() . "|" .
		$progressivoFattura->getNumFatturaUltimo() . "|" .
		$progressivoFattura->getNotaTestaFattura() . "|" .
		$progressivoFattura->getNotaPiedeFattura();

		echo $datiPagina;
	}

	public function go()
	{
		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		if ($this->controlliLogici($progressivoFattura)) {

			// Aggiornamento del DB ------------------------------

			if ($progressivoFattura->update($db)) {
				$_SESSION[self::MSG_DA_MODIFICA_PROGRESSIVO] = self::AGGIORNA_PROGRESSIVO_OK;
			}
		}
		else {
			$_SESSION[self::MSG_DA_MODIFICA_PROGRESSIVO] = $_SESSION[self::MESSAGGIO];
		}

		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaProgressivoFattura::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function controlliLogici($progressivoFattura) {

		$esito = TRUE;
		$msg = "<br>";

		if ($msg != "<br>") {
			$_SESSION["messaggio"] = $msg;
		}
		else {
			unset($_SESSION["messaggio"]);
		}
		return $esito;
	}
}

?>