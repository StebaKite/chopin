<?php

require_once 'anagrafica.abstract.class.php';

class CercaCfisCliente extends AnagraficaAbstract {

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

			self::$_instance = new CercaCfisCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		echo "C.fisc Ok!";
	}

	/**
	 * 30/3/2017 : disattivato il controllo in attesa della 4.0
	 */
// 	public function start() {
		
// 		require_once 'database.class.php';
// 		require_once 'utility.class.php';

// 		$db = Database::getInstance();
// 		$utility = Utility::getInstance();
		
// 		$result = $this->cercaCodiceFiscaleCliente($db, $utility, $_SESSION["codfisc"]);
		
// 		if ($result){
// 			if (pg_num_rows($result) > 0) {
// 				foreach(pg_fetch_all($result) as $row) {
// 					echo "C.fisc cliente gi&agrave; usato da : " . $row['des_cliente'];
// 					break;
// 				}
// 			}
// 			else {
// 				echo "C.fisc Ok!";				
// 			}
// 		}
// 		else {
// 			echo "ATTENZIONE!! Errore controllo codice fiscale cliente";				
// 		}
// 	}
}
				
?>