<?php

require_once 'nexus6.abstract.class.php';

abstract class ConfigurazioniAbstract extends Nexus6Abstract {

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

	// Metodi comuni di utilita della prima note ---------------------------



}

?>
