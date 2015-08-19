<?php

require_once 'configurazioni.abstract.class.php';

class CancellaCausale extends ConfigurazioniAbstract {

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

			self::$_instance = new CancellaCausale();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaCausale.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$this->cancellaCausale($db, $utility, $_SESSION["codcausale"]);

		$_SESSION["messaggioCancellazione"] = "Causale " . $_SESSION['codcausale'] . " cancellata";
		$ricercaCausale = RicercaCausale::getInstance();
		$ricercaCausale->go();
	}
}

?>