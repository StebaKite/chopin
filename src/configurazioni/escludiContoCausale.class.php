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

		$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);

		$datiPagina =
		trim($this->makeTableContiConfigurati($configurazioneCausale)) . "|" .
		trim($this->makeTableContiConfigurabili($configurazioneCausale));

		echo $datiPagina;
	}

	public function go() {}
}

?>