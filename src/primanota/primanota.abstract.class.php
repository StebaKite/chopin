<?php

require_once 'chopin.abstract.class.php';

abstract class primanotaAbstract extends chopinAbstract {

	public static $messaggio;

	
	
	
	// Query --------------------------------------------------------------- 
	

	
	
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
