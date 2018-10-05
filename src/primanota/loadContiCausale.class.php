<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class LoadContiCausale extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public static function getInstance()
	{
		if (!isset($_SESSION[self::LOAD_CONTI_CAUSALE])) $_SESSION[self::LOAD_CONTI_CAUSALE] = serialize(new LoadContiCausale());
		return unserialize($_SESSION[self::LOAD_CONTI_CAUSALE]);
	}

	public function start()
	{
		$causale = Causale::getInstance();
		$db = Database::getInstance();

		if ($causale->loadContiConfigurati($db)) echo $causale->getContiCausale();
		else echo EMPTYSTRING;
	}
	public function go() {
		$this->start();
	}
}

?>