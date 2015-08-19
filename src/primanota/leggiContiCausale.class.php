<?php

require_once 'primanota.abstract.class.php';

class LeggiContiCausale extends PrimanotaAbstract {

	public static $replace;
	public static $elenco_conti;
	public static $queryRicercaConti = "/primanota/ricercaConti.sql";
	
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

			self::$_instance = new LeggiContiCausale();

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
				'%cod_causale%' => trim($_SESSION["causale"])
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaConti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);

		self::$elenco_conti = "<select class='selectmenuConto' id='conti' name='conti'>";
		
		if (pg_num_rows($result) > 0) {
			self::$elenco_conti = "<option value=''></option>";
			while ($row = pg_fetch_row($result)) {
				self::$elenco_conti = self::$elenco_conti . "<option value='" . $row[0] . $row[1] . " - " . $row[2] . "'>" . $row[2] . "</option>" ;
			}
		}
		else {
			self::$elenco_conti = self::$elenco_conti . "<option value=''>Non ci sono conti configurati per la causale</option>";
		}		
		self::$elenco_conti = self::$elenco_conti . "</select>";
		echo self::$elenco_conti;
	}
}

?>