<?php

require_once 'anagrafica.abstract.class.php';

class CercaPivaCliente extends AnagraficaAbstract {

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

			self::$_instance = new CercaPivaCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		echo "P.iva Ok!";
	}

	/**
	 * 30/4/2017 : Disattivato il controllo in attesa della 4.0
	 */
// 	public function start() {
		
// 		require_once 'database.class.php';
// 		require_once 'utility.class.php';

// 		$db = Database::getInstance();
// 		$utility = Utility::getInstance();

// 		$idcliente = $_SESSION["idcliente"];
		
// 		if (!is_numeric($idcliente))
// 			$idcliente = ($_SESSION["idcliente"] != "") ? $this->leggiDescrizioneCliente($db, $utility, $_SESSION["idcliente"]) : "null";
		
// 		$result = $this->cercaPartivaIvaCliente($db, $utility, $_SESSION["codpiva"], $idcliente); 
		
// 		if ($result){
// 			if (pg_num_rows($result) > 0) {
// 				foreach(pg_fetch_all($result) as $row) {
// 					echo "P.iva cliente gi&agrave; esistente: " . $row['des_cliente'];
// 					break;
// 				}
// 			}
// 			else {
// 				echo "P.iva Ok!";				
// 			}
// 		}
// 		else {
// 			echo "Controllo partita iva cliente non eseguito!";				
// 		}
// 	}
}
				
?>