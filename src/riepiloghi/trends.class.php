<?php

require_once 'riepiloghi.abstract.class.php';

class Trends extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $queryTrendPagamenti = "/riepiloghi/trendPagamenti.sql";
	public static $queryTrendIncassi = "/riepiloghi/trendIncassi.sql";
	
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

			self::$_instance = new Trends();

		return self::$_instance;
	}

	public function start() {

		require_once 'trends.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$trendsTemplate = TrendsTemplate::getInstance();
		
		$this->ricercaDati($utility);				
		$this->preparaPagina($trendsTemplate);
			
		include(self::$testata);
		$trendsTemplate->displayPagina();
		echo $utility->tailTemplate($template);				
		include(self::$piede);
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$this->ricercaPagamenti($utility, $db, "VIL");
		$this->ricercaIncassi($utility, $db, "VIL");
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
	
		if (pg_num_rows($result) > 0) {
			$_SESSION["trendPagamenti"] = $result;
		}
		else {
			unset($_SESSION["trendPagamenti"]);
		}
		return $result;
	}

	public function ricercaIncassi($utility, $db, $negozio) {
	
		$array = $utility->getConfig();
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => $negozio
		);
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrendIncassi;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION["trendIncassi"] = $result;
		}
		else {
			unset($_SESSION["trendIncassi"]);
		}
		return $result;
	}
	
	public function preparaPagina($trendsTemplate) {
		
		$_SESSION["titoloPagina"] = "%ml.incassiPagamenti%";
	}
}

?>