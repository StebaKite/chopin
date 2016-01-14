<?php

require_once 'configurazioni.abstract.class.php';

class InserisciSottoconto extends ConfigurazioniAbstract {

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

			self::$_instance = new InserisciSottoconto();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function go() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'modificaConto.class.php';
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$db->beginTransaction();
		
		if ($this->inserisciSottoconto($db, $utility, $_SESSION["codconto"], $_SESSION["codsottoconto"], $_SESSION["dessottoconto"])) {
			$db->commitTransaction();
			$_SESSION["messaggio"] = "Conto salvato con successo";				
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore inserimento sottoconto, eseguito Rollback");
			$_SESSION["messaggio"] = "Attenzione: conto non inserito!";				
		}
		
		$modificaConto = ModificaConto::getInstance();
		$modificaConto->start();		
	}
}

?>