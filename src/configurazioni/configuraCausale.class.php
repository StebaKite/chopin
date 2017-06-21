<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configuraCausale.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'configuraCausale.class.php';
require_once 'configurazioneCausale.class.php';

class ConfiguraCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

	const ERRORE_LETTURA = "Errore fatale durante la lettura dei dati";
	const ERRORE_SCRITTURA = "Errore fatale durante la scrittura dei dati";

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CONFIGURA_CAUSALE])) $_SESSION[self::CONFIGURA_CAUSALE] = serialize(new ConfiguraCausale());
		return unserialize($_SESSION[self::CONFIGURA_CAUSALE]);
	}

	public function start()
	{
		$configurazioneCausale = ConfigurazioneCausale::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$this->refreshContiConfigurati($db, $configurazioneCausale);
		$this->refreshContiConfigurabili($db, $configurazioneCausale);

		$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);

		$datiPagina =
		trim($this->makeTableContiConfigurati($configurazioneCausale)) . "|" .
		trim($this->makeTableContiConfigurabili($configurazioneCausale));

		echo $datiPagina;
	}

	public function go() {}

}

?>