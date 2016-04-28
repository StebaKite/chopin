<?php

require_once 'primanota.abstract.class.php';

class CercaFatturaCliente extends PrimanotaAbstract {

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

			self::$_instance = new CercaFatturaCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$db->beginTransaction();

		$id_cliente = $this->leggiDescrizioneCliente($db, $utility, $_SESSION["idcliente"]);
		$result = $this->cercaFatturaCliente($db, $utility, $id_cliente, $_SESSION["numfatt"], $_SESSION["datareg"]); 
		
		if ($result) {
			if (pg_num_rows($result) > 0) {
				foreach(pg_fetch_all($result) as $row) {
					echo "Fattura gi&agrave; esistente!";
				}
			}
			else {
				echo "Numero fattura Ok!";				
			}
		}
		else {
			echo "Controllo numero fattura non eseguito!";				
		}
		$db->commitTransaction();	
	}
}
				
?>