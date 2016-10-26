<?php

require_once 'primanota.abstract.class.php';

class ControllaDataRegistrazione extends PrimanotaAbstract {

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

			self::$_instance = new ControllaDataRegistrazione();

			return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$dataOk = false;
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$db->beginTransaction();		
		
		$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
		
		if ($lavoriPianificati) {
			
			$rows = pg_fetch_all($lavoriPianificati);
		
			foreach($rows as $row) {
		
				/**
				 * Se la registrazione ha una data di registrazione che cade all'interno di un mese in linea è ok.
				 * Salto tutti gli eventuali lavori pianificati che cadono in giorni diversi dal primo del mese
				 */
		
				if (date("d", strtotime($row['dat_lavoro'])) == "01") {
					
					$dataRegistrazione = strtotime(str_replace('/', '-', $_SESSION["datareg"]));
											
					if ($dataRegistrazione >= strtotime($row['dat_lavoro'])) {
						$dataOk = true;
						break;
					}
				}
			}
		}
		
		$db->commitTransaction();
		
		if ($dataOk) echo "";
		else echo "Data non ammessa";		
	}
}

?>