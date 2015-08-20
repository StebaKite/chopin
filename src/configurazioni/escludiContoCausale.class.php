<?php

require_once 'configurazioni.abstract.class.php';

class EscludiContoCausale extends ConfigurazioniAbstract {

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

			self::$_instance = new EscludiContoCausale();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'utility.class.php';
		require_once 'configuraCausale.class.php';

		$utility = Utility::getInstance();
		$this->cancellaContoCausale($utility);
			
		$configuraCausale = ConfiguraCausale::getInstance();
		$configuraCausale->start();
	}

	public function go() {}

	private function cancellaContoCausale($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		$db->beginTransaction();
		if ($this->deleteConfigurazioneCausale($db, $utility, $_SESSION["codcausale"], $_SESSION["codconto"])) {
			$db->commitTransaction();
			return TRUE;
		}
		return FALSE;
	}
}

?>