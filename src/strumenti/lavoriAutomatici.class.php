<?php

require_once 'chopin.abstract.class.php';

class LavoriAutomatici extends ChopinAbstract {

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
	
			self::$_instance = new LavoriAutomatici();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------

	public function start() {
			
		require_once 'lavoriAutomatici.template.php';
		require_once 'utility.class.php';
		require_once 'database.class.php';
		
		$utility = Utility::getInstance();
		
		$lavoriAutomaticiTemplate = LavoriAutomaticiTemplate::getInstance();

		$array = $utility->getConfig();

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			
			$db = Database::getInstance();

			$db->beginTransaction();
				
			$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
						
			if ($lavoriPianificati) {

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
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);
		
		$lavoriAutomaticiTemplate->displayPagina();
		include(self::$piede);
	}		
}

?>
