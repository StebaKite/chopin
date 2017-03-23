<?php

require_once 'primanota.abstract.class.php';

class ControlloCorrispettivo extends PrimanotaAbstract {

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

			self::$_instance = new ControlloCorrispettivo();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();
			
		$result = $this->cercaCorrispettivo($db, $utility, $_SESSION["datareg"], $_SESSION["codneg"], $_SESSION["conto"], $_SESSION["causale"], $_SESSION["importo"]);
			
		if ($result) {
			if (pg_num_rows($result) > 0) {
				foreach(pg_fetch_all($result) as $row) {
					echo "Corrispettivo gi&agrave; esistente: " . $row['id_registrazione'] . " , " . $row['des_registrazione'];
					break;
				}
			}
			else {
				echo "Corrispettivo ok";
			}
		}
		else {
			echo "Controllo corrispettivo non eseguito!";
		}				
	}
}
				
?>