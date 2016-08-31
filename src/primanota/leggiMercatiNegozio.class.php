<?php

require_once 'primanota.abstract.class.php';

class LeggiMercatiNegozio extends PrimanotaAbstract {

	public static $replace;
	public static $elenco_mercati;
	public static $queryRicercaMercati = "/primanota/ricercaMercati.sql";

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

			self::$_instance = new LeggiMercatiNegozio();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		$array = $utility->getConfig();
	
		self::$replace = array(
			'%cod_negozio%' => trim($_SESSION["negozio"]),
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaMercati;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);
	
		self::$elenco_mercati = "<select class='selectmenuMercato' id='mercati' name='mercati'><option value=''></option>";
	
		if (pg_num_rows($result) > 0) {
			while ($row = pg_fetch_row($result)) {
				self::$elenco_mercati = self::$elenco_mercati . "<option value='" . $row[0] . "'>" . $row[1] . "</option>" ;
			}
		}
		else {
			self::$elenco_mercati = self::$elenco_mercati . "<option value=''>Non ci sono mercati per il negozio</option>";
		}
		self::$elenco_mercati = self::$elenco_mercati . "</select>";
		echo self::$elenco_mercati;
	}
}
	
?>