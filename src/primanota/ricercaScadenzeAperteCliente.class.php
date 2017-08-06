<?php

require_once 'primanota.abstract.class.php';

class RicercaScadenzeAperteCliente extends PrimanotaAbstract {

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

			self::$_instance = new RicercaScadenzeAperteCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$options = '<select class="numfatt-cliente-multiple" multiple="multiple" style="width: 600px" id="select2">';

		if ($_SESSION["descli"] != "") {
			$db = Database::getInstance();
			$utility = Utility::getInstance();

			$db->beginTransaction();
			$_SESSION["idcliente"] = $this->leggiDescrizioneCliente($db, $utility, str_replace("'", "''", $_SESSION["descli"]));

			$result_scadenze_cliente = $this->prelevaScadenzeAperteCliente($db, $utility, $_SESSION["idcliente"], $_SESSION["negozio"]);

			foreach(pg_fetch_all($result_scadenze_cliente) as $row) {
				$options .= '<option value="' . trim($row['num_fattura']) . '" >Ft.' . trim($row['num_fattura']) . ' - &euro; ' . trim($row['imp_registrazione']) . ' - (' . trim($row['nota']) . ')</option>';
			}
		}
		$options .= '</select>';
		$db->commitTransaction();
		echo $options;
	}
}

?>