<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaConto.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';

class ControllaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public function getInstance()
	{
		if (!isset($_SESSION[self::CONTROLLA_CONTO])) $_SESSION[self::CONTROLLA_CONTO] = serialize(new ControllaConto());
		return unserialize($_SESSION[self::CONTROLLA_CONTO]);
	}
	
	public function start()
	{
		$conto = Conto::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		if ($conto->leggi($db) > 0) echo "Conto presente";
		else echo "";
	}
	
	public function go() {
		$this->start();
	}
}

?>