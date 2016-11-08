<?php

require_once 'fattura.abstract.class.php';

class PrelevaTipoAddebitoCliente extends FatturaAbstract {

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

			self::$_instance = new PrelevaTipoAddebitoCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
		
		require_once 'database.class.php';
		require_once 'utility.class.php';

		/**
		 * Questi dati vengono ricaricati in sessione dalla funzione caricaTipoAddebitoCliente
		 */
		unset($_SESSION["indirizzocliente"]);
		unset($_SESSION["cittacliente"]);
		unset($_SESSION["capcliente"]);
		unset($_SESSION["pivacliente"]);
		unset($_SESSION["cfiscliente"]);
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$db->beginTransaction();
		$idcliente = $this->leggiDescrizioneCliente($db, $utility, $_SESSION["descliente"]);
		$db->commitTransaction();
		
		$tipoAddebitoCliente = $this->caricaTipoAddebitoCliente($utility, $db, $idcliente);

		echo $tipoAddebitoCliente;		
	}
}
				
?>