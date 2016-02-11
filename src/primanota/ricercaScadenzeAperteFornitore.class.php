<?php

require_once 'primanota.abstract.class.php';

class RicercaScadenzeAperteFornitore extends PrimanotaAbstract {

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

			self::$_instance = new RicercaScadenzeAperteFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$options = '<select class="numfatt-multiple" multiple="multiple" style="width: 300px" id="select2">';

		$db->beginTransaction();
		$_SESSION["idfornitore"] = $this->leggiDescrizioneFornitore($db, $utility, $_SESSION["desforn"]);
		$db->commitTransaction();		
		
		$result_scadenze_fornitore = $this->prelevaScadenzeAperteFornitore($db, $utility, $_SESSION["idfornitore"]);

		foreach(pg_fetch_all($result_scadenze_fornitore) as $row) {
			$options .= '<option value="' . trim($row['num_fattura']) . '">' . trim($row['num_fattura']) . '</option>';
		}		
		$options .= '</select>';
		
		echo $options;
	}
}

?>