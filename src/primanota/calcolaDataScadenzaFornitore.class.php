<?php

require_once 'primanota.abstract.class.php';

class CalcolaDataScadenzaFornitore extends PrimanotaAbstract {

	public static $ggMese = array(
			'01' => '31',
			'02' => '28',
			'03' => '31',
			'04' => '30',
			'05' => '31',
			'06' => '30',
			'07' => '31',
			'08' => '31',
			'09' => '30',
			'10' => '31',
			'11' => '30',
			'12' => '31',
	);	
	
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

		$db->beginTransaction();
		$idfornitore = $this->leggiDescrizioneFornitore($db, $utility, $_SESSION["desfornitore"]);
		$db->commitTransaction();
		
		$result_fornitore = $this->prelevaIdFornitore($db, $utility, $idfornitore); 
		
		foreach(pg_fetch_all($result_fornitore) as $row) {
			$num_gg_scadenza_fattura = $row['num_gg_scadenza_fattura'];
		}

		/**
		 * Se i giorni scadenza fattura del fornitore sono = 0 non viene calcolata da data scadenza 
		 */
		if ($num_gg_scadenza_fattura > 0) {
			/**
			 * Le data di registrazione viene aumentata dei giorni configurati per il fornitore, alla data ottenuta viene sostituito il
			 * giorno con l'ultimo giorno del mese corrispondente
			 */
			
			$dataScadenza = $this->sommaGiorniData($_SESSION["datareg"], "/", $num_gg_scadenza_fattura);
			
			$data = explode("/",$dataScadenza);
			$mese = $data[1];
			$anno = $data[2];
			
			echo SELF::$ggMese[$mese]."/".$mese."/".$anno;				
		}
		else {
			echo "";
		}
	}
}
				
?>