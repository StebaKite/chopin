<?php

require_once 'primanota.abstract.class.php';

class CercaFatturaFornitore extends PrimanotaAbstract {

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

			self::$_instance = new CercaFatturaFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		if ($_SESSION["causale"] != "1100") {
			$result = $this->cercaFatturaFornitore($db, $utility, $_SESSION["idfornitore"], $_SESSION["numfatt"]);
			
			if ($result){
				if (pg_num_rows($result) > 0) {
					foreach(pg_fetch_all($result) as $row) {
						echo "Numero fattura gi&agrave; esistente: " . date("d/m/Y",strtotime($row['dat_registrazione'])) . " , " . $row['des_registrazione'];
					}
				}
				else {
					echo "Numero fattura Ok!";
				}
			}
			else {
				echo "Controllo numero fattura non eseguito!";
			}				
		}
		else {
			echo "";
		}
	}
}
				
?>