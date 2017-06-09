<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'modificaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class ModificaGruppoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_GRUPPO_SOTTOCONTO])) $_SESSION[self::MODIFICA_GRUPPO_SOTTOCONTO] = serialize(new ModificaGruppoSottoconto());
		return unserialize($_SESSION[self::MODIFICA_GRUPPO_SOTTOCONTO]);
	}

	public function start() { $this->go(); }

	public function go()
	{
		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$db = Database::getInstance();

		$sottoconto->aggiorna($db);
		echo $this->makeTabellaSottoconti($conto, $sottoconto);
	}
}

?>