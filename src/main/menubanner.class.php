<?php

require_once 'chopin.abstract.class.php';

class Menubanner extends ChopinAbstract {

	private static $messaggio;
	
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
	
			self::$_instance = new Menubanner();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------

	public function start() {
			
		require_once 'menubanner.template.php';
		require_once 'utility.class.php';
		require_once 'database.class.php';
		
		$utility = Utility::getInstance();
		
		$menubannerTemplate = MenubannerTemplate::getInstance();

		$array = $utility->getConfig();

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			
			$db = Database::getInstance();

			$db->beginTransaction();
				
			$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
						
			if ($lavoriPianificati) {
				
				$this->eseguiLavoriPianificati($db, $lavoriPianificati);

				/**
				 * Refresh della tabellina in sessione dei lavori pianificati e commit della transazione.
				 * Attenzione che la transazione rimane aperta per tutti i lavori pianificati
				 */
				$lavoriPianificati = $this->leggiLavoriPianificatiAnnoCorrente($db, $utility);
				$_SESSION["lavoriPianificati"] = pg_fetch_all($lavoriPianificati);								
				$db->commitTransaction();
			}
			else {
				unset($_SESSION["lavoriPianificati"]);
			}
		}
		else {
			error_log("Lavori pianificati non attivi!!");
		}
		
		// compone la pagina
		include($testata);
		$menubannerTemplate->displayPagina();
		include($piede);
	}		
}

?>
