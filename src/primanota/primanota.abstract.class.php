<?php

require_once 'chopin.abstract.class.php';

abstract class primanotaAbstract extends chopinAbstract {

	public static $messaggio;

	
	
	
	// Query --------------------------------------------------------------- 
	
	public static $queryRicercaCausali = "/primanota/ricercaCausali.sql";	
	public static $queryRicercaFornitori = "/primanota/ricercaFornitori.sql";
	
	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}
	
	// ------------------------------------------------
	
	public function getMessaggio() {
		return self::$messaggio;
	}	
}

?>
