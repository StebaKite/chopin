<?php

require_once 'chopin.abstract.class.php';

class ChiudiEvento extends ChopinAbstract {

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

			self::$_instance = new ChiudiEvento();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function go() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'corpo.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$db->beginTransaction();

		if ($this->chiudiEvento($db, $utility, $_SESSION["idevento"], $_SESSION["staevento"])) {

			$db->commitTransaction();
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento evento, eseguito Rollback");
		}

		$corpo = Corpo::getInstance();
		$corpo->start();
	}
}

?>