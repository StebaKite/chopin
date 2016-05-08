<?php

require_once 'anagrafica.abstract.class.php';

class CancellaCliente extends AnagraficaAbstract {

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

			self::$_instance = new CancellaCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaCliente.class.php';
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$this->cancellaCliente($db, $utility, $_SESSION["idcliente"]);
		
		$_SESSION["messaggioCancellazione"] = "Cliente " . $_SESSION['codclienteselezionato'] . " cancellato";
		$ricercaCliente = RicercaCliente::getInstance();
		$ricercaCliente->start();
	}
}	
		
?>