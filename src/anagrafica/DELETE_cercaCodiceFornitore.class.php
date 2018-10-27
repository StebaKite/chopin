<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';

class CercaCodiceFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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

			self::$_instance = new CercaCodiceFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$result = $this->cercaCodiceFornitore($db, $utility, $_SESSION["codfornitore"]); 
		
		if ($result){
			if (pg_num_rows($result) > 0) {
				foreach(pg_fetch_all($result) as $row) {
					echo "Codice fornitore gi&agrave; esistente: " . $row['des_fornitore'];
					break;
				}
			}
			else {
				echo "Codice fornitore Ok!";				
			}
		}
		else {
			echo "Controllo codice fornitore non eseguito!";				
		}
	}
	
	public function go() {}
}
				
?>