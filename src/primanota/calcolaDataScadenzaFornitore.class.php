<?php

require_once 'primanota.abstract.class.php';

class CalcolaDataScadenzaFornitore extends PrimanotaAbstract {

	public static $replace;

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CalcolaDataScadenzaFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$result_fornitore = $this->prelevaIdFornitore($db, $utility, $_SESSION["idfornitore"]); 
		foreach(pg_fetch_all($result_fornitore) as $row) {
			$num_gg_scadenza_fattura = $row['num_gg_scadenza_fattura'];
		}

		/**
		 * Le data odierna viene aumentata dei giorni configurati per il fornitore, alla data ottenuta viene sostituito il
		 * giorno con l'ultimo giorno del mese corrispondente
		 */
		$ggMese = array(
				'1' => '31',
				'2' => '28',
				'3' => '31',
				'4' => '30',
				'5' => '31',
				'6' => '30',
				'7' => '31',
				'8' => '31',
				'9' => '30',
				'10' => '31',
				'11' => '31',
				'12' => '31',
		);
		
		$dataScadenza = $this->sommaGiorniData(date("d/m/Y"), "/", $num_gg_scadenza_fattura);
		$mese = substr($dataScadenza, 3, 2);
				
		echo $ggMese[$mese].substr($dataScadenza,2);
	}
}
				
?>