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

		$options = '<select class="numfatt-multiple" multiple="multiple" style="width: 600px" id="select2">';

		if ($_SESSION["desforn"] != "") {
			$db = Database::getInstance();
			$utility = Utility::getInstance();

			$db->beginTransaction();
			$_SESSION["idfornitore"] = $this->leggiDescrizioneFornitore($db, $utility, str_replace("'", "''", $_SESSION["desforn"]));

			$result_scadenze_fornitore = $this->prelevaScadenzeAperteFornitore($db, $utility, $_SESSION["idfornitore"], $_SESSION["negozio"]);

			foreach(pg_fetch_all($result_scadenze_fornitore) as $row) {
				$options .= '<option value="' . trim($row['num_fattura']) . '">' . trim($row['num_fattura']) . ' - &euro; ' . trim($row['imp_in_scadenza']) . ' - (' . trim($row['nota_scadenza']) . ')</option>';
			}
		}
		$options .= '</select>';
		$db->commitTransaction();

		echo $options;
	}
}

?>