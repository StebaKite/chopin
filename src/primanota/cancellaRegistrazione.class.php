<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'ricercaRegistrazione.class.php';

class CancellaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public function getInstance()
	{
		if (!isset($_SESSION[self::CANCELLA_REGISTRAZIONE])) $_SESSION[self::CANCELLA_REGISTRAZIONE] = serialize(new CancellaRegistrazione());
		return unserialize($_SESSION[self::CANCELLA_REGISTRAZIONE]);
	}

	public function start() { }
	
	public function go()
	{
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		$registrazione = Registrazione::getInstance();
		
		$registrazione->leggi($db);
		
		$db->beginTransaction();
		
		$registrazione->cancella($db);
		
		/**
		 * Rigenero i saldi
		 */
		
		$this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
		$db->commitTransaction();
		
		$ricercaRegistrazione = RicercaRegistrazione::getInstance();
		$ricercaRegistrazione->go();	
	}
}

?>