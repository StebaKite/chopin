<?php

require_once 'primanota.abstract.class.php';

class InserisciDettaglioRegistrazione extends primanotaAbstract {

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

			self::$_instance = new InserisciDettaglioRegistrazione();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function go() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'modificaRegistrazione.class.php';
		
		$utility = Utility::getInstance();		
		$db = Database::getInstance();
		
		$db->beginTransaction();
		
		$importo = $_SESSION["importo"];
		
		$cc = explode(" - ", $_SESSION["conti"]);
		
		$conto = substr(trim($cc[0]), 0, 3);
		$sottoconto = substr(trim($cc[0]), 3);
		$d_a = $_SESSION["dareavere"];
		
		if ($this->inserisciDettaglioRegistrazione($db, $utility, $_SESSION["idRegistrazione"], $conto, $sottoconto, $importo, $d_a)) {

			$db->commitTransaction();
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore inserimento dettaglio registrazione, eseguito Rollback");
		}		
		
		$modificaRegistrazione = ModificaRegistrazione::getInstance();
		$modificaRegistrazione->go();
	}	
}

?>