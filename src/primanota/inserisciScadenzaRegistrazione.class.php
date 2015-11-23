<?php

require_once 'primanota.abstract.class.php';

class InserisciScadenzaRegistrazione extends primanotaAbstract {

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

			self::$_instance = new InserisciScadenzaRegistrazione();

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
		
		$descreg = $_SESSION["descreg"];
		$datascad = ($_SESSION["datascadsuppl"] != "") ? "'" . $_SESSION["datascadsuppl"] . "'" : "null" ;
		$importo = $_SESSION["importosuppl"];
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$fornitore = ($_SESSION["fornitore"] != "") ? $_SESSION["fornitore"] : "null" ;				
		$staScadenza = "00";

		$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
		foreach(pg_fetch_all($result_fornitore) as $row) {
			$tipAddebito_fornitore = $row['tip_addebito'];
		}
		
		/**
		 * Conto le scadenze per determinare il progressivo fattura da usare
		 */
		$progrFattura = 0;
		foreach ($_SESSION["elencoScadenzeRegistrazione"] as $row) {		
			$progrFattura += 1;
		}		
		$progrFattura += 1;
		$numfatt_generato = "'" . trim($_SESSION["numfatt"]) . "." . $progrFattura . "'";
		
		if ($this->inserisciScadenza($db, $utility, $_SESSION["idRegistrazione"], $datascad, $importo,
			$descreg, $tipAddebito_fornitore, $codneg, $fornitore, $numfatt_generato, $staScadenza)) {

			$db->commitTransaction();
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore inserimento scadenza registrazione, eseguito Rollback");
		}		
		
		$modificaRegistrazione = ModificaRegistrazione::getInstance();
		$modificaRegistrazione->start();
	}	
}

?>