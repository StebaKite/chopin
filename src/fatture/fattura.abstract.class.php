<?php

require_once 'chopin.abstract.class.php';

abstract class FatturaAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

// 	public static $queryLeggiFornitore = "/anagrafica/ricercaCodiceFornitore.sql";

	function __construct() {
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new FatturaAbstract();

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


	
	
	
	
	
	
	
}

?>