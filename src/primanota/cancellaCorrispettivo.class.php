<?php

require_once 'primanota.abstract.class.php';

class CancellaCorrispettivo extends primanotaAbstract {

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

			self::$_instance = new CancellaCorrispettivo();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaCorrispettivo.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$db->beginTransaction();
		
		$this->cancellaRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);

		$db->commitTransaction();
		
		$_SESSION["messaggioCancellazione"] = "Corrispettivo numero " . $_SESSION['idRegistrazione'] . " cancellato";
		$ricercaCorrispettivo = RicercaCorrispettivo::getInstance();
		$ricercaCorrispettivo->go();
	}
}

?>