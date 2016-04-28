<?php

require_once 'chopin.abstract.class.php';

class Spalla extends ChopinAbstract {

	private static $messaggio;

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new Spalla();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'spalla.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$spallaTemplate = SpallaTemplate::getInstance();
		
		// compone la pagina
		$spallaTemplate->displayPagina();
	}
}

?>