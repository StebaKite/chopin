<?php

require_once 'chopin.abstract.class.php';

abstract class ScadenzeAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryRicercaScadenze = "/scadenze/ricercaScadenze.sql";

	function __construct() {
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new ScadenzeAbstract();

		return self::$_instance;
	}

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima note ---------------------------
	
	public function leggiScadenze($db, $utility, $datascad_da, $datascad_a) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%dat_scadenza_da%' => $datascad_da,
				'%dat_scadenza_a%' => $datascad_a
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenze;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
}

?>