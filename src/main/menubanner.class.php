<?php

require_once 'chopin.abstract.class.php';

class Menubanner extends ChopinAbstract {

	private static $messaggio;
	private static $queryTotaliProgressivi = "/main/totaliProgressivi.sql";
	
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

		error_log("<<<<<<< Start >>>>>>> " . $_SERVER['PHP_SELF']);
		
		$menubannerTemplate = MenubannerTemplate::getInstance();
		
		/**
		 *  qui ci metto la gestione dei lavori in piano
		 *  se ci sono lavori da eseguire pianificati in una data =< alla data odierna
		 *  	allora vengono eseguiti uno alla volta
		 *  	e imposta un messaggio: <nomelavoro> " Eseguito con successo"
		 *  altrimenti
		 *  	imposta un messaggio "Prossimo: " <nomelavoro> " il " <data> 
		 */
		 
		
		
		
// 		// Template
// 		$utility = new utility();
// 		$db = new database();

// 		$array = $utility->getConfig();

// 		$testata = self::$root . $array['testataPagina'];
// 		$piede = self::$root . $array['piedePagina'];		

// 		$menubannerTemplate = new menubannerTemplate();
		
// 		//-------------------------------------------------------------
		
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryTotaliProgressivi;
// 		$sql = $utility->getTemplate($sqlTemplate);
// 		$result = $db->getData($sql);
			
// 		if ($result) $menubannerTemplate->setTotaliProgressivi(pg_fetch_all($result));
// 		else $menubannerTemplate->setTotaliProgressivi("");
		
		// compone la pagina
		include($testata);
		$menubannerTemplate->displayPagina();
		include($piede);
	}
}

?>
