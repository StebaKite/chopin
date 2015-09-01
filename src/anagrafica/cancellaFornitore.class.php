<?php

require_once 'anagrafica.abstract.class.php';

class CancellaFornitore extends AnagraficaAbstract {

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

			self::$_instance = new CancellaFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaFornitore.class.php';
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$this->cancellaFornitore($db, $utility, $_SESSION["idfornitore"]);
		
		$_SESSION["messaggioCancellazione"] = "Fornitore " . $_SESSION['codfornitoreselezionato'] . " cancellato";
		$ricercaFornitore = RicercaFornitore::getInstance();
		$ricercaFornitore->go();
	}
}	
		
?>