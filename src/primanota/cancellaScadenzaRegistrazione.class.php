<?php

require_once 'primanota.abstract.class.php';

class CancellaScadenzaRegistrazione extends primanotaAbstract {

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

			self::$_instance = new CancellaScadenzaRegistrazione();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function go() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'modificaRegistrazione.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$this->cancellaScadenzaRegistrazione($db, $utility, $_SESSION["idScadenzaRegistrazione"]);

		$modificaRegistrazione = ModificaRegistrazione::getInstance();
		$modificaRegistrazione->start();
	}
}

?>