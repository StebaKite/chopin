<?php

require_once 'chopin.abstract.class.php';

class Corpo extends ChopinAbstract {

	public static $messaggio;
	public static $queryEventi = "/main/eventi.sql";
	public static $queryTrendPagamenti = "/riepiloghi/trendPagamenti.sql";

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
		
		$corpoTemplate = CorpoTemplate::getInstance();
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		//- Box degli eventi ------------------------------------------------------------

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

		//- Box trends ------------------------------------------------------------

		if ($array['primoSaldoDisponibile'] != "") { $_SESSION["datareg_da"] = $array['primoSaldoDisponibile']; }
		else { $_SESSION["datareg_da"] = "01/01/" . date("Y"); }
		
		if ($array['ultimoSaldoDisponibile'] != "") { $_SESSION["datareg_a"] = $array['ultimoSaldoDisponibile']; }
		else { $_SESSION["datareg_a"] = "31/12/" . date("Y"); }
		
		$this->ricercaPagamenti($utility, $db, "VIL");
		$this->ricercaPagamenti($utility, $db, "BRE");
		$this->ricercaPagamenti($utility, $db, "TRE");
		
		// compone la pagina
		include($testata);
		$corpoTemplate->displayPagina();
		include($piede);
	}	

	public function ricercaPagamenti($utility, $db, $negozio) {
	
		$array = $utility->getConfig();
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => $negozio
		);
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrendPagamenti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		$dati = "trendPagamenti" . $negozio;
		
		if (pg_num_rows($result) > 0) {
			$_SESSION[$dati] = $result;
		}
		else {
			unset($_SESSION[$dati]);
		}
		return $result;
	}
}

?>