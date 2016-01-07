<?php

require_once 'fattura.abstract.class.php';

class AggiornaProgressivoFattura extends FatturaAbstract {

	public static $replace;

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new AggiornaProgressivoFattura();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		if ($this->aggiornaNumeroFattura($utility, $db, $_SESSION["catcliente"], $_SESSION["codneg"], $_SESSION["numfatt"])) {
			echo $_SESSION["numfatt"];				
		}
		echo "?";		
	}
}
				
?>