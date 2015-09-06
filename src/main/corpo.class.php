<?php

require_once 'chopin.abstract.class.php';

class Corpo extends ChopinAbstract {

	private static $messaggio;
	private static $queryEventi = "/main/eventi.sql";

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

			self::$_instance = new Corpo();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'corpo.template.php';
		require_once 'utility.class.php';
		require_once 'database.class.php';
		
		error_log("<<<<<<< Start >>>>>>> " . $_SERVER['PHP_SELF']);
		
		$corpoTemplate = CorpoTemplate::getInstance();
		
		$utility = new utility();
		$db = new database();
		
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		//-------------------------------------------------------------

		$filtroEventi = "";
		
		if ($_SESSION["statoeventi"] != "") {
			$filtroEventi = "WHERE sta_evento = '" . $_SESSION["statoeventi"] . "'"; 			
		} 
		
		$replace = array(
				'%filtro_eventi%' => $filtroEventi
		);
		
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryEventi;
		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if ($result) $_SESSION["eventi"] = pg_fetch_all($result);
		else $_SESSION["eventi"] = "";
		
		// compone la pagina
		include($testata);
		$corpoTemplate->displayPagina();
		include($piede);
	}	
}

?>