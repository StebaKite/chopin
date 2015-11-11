<?php

require_once 'configurazioni.abstract.class.php';

class CancellaSottoconto extends ConfigurazioniAbstract {

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

			self::$_instance = new CancellaSottoconto();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'modificaConto.class.php';
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$this->cancellaSottoconto($db, $utility, $_SESSION["codconto"], $_SESSION["codsottoconto"]);
		
		$modificaConto = ModificaConto::getInstance();
		$modificaConto->start();
	}
}

?>