<?php

//require_once 'primanota.abstract.class.php';

class Sottoconto {

	private static $_conto;
	private static $_sottoconto;
	private static $_des_sottoconto;
	
	function __construct($cod_conto, $cod_sottoconto, $des_sottoconto) {
		
		$this->set_conto($cod_conto);
		$this->set_sottoconto($cod_sottoconto);
		$this->set_des_sottoconto($des_sottoconto);		
	}
	
	function get_conto() {
		return self::$_conto;
	}

	function get_settoconto() {
		return self::$_sottoconto;
	}
		
	function get_des_sottoconto() {
		return self::$_des_sottoconto;
	}
	
	function set_conto($conto) {
		self::$_conto = $conto;
	}
	
	function set_sottoconto($sottoconto) {
		self::$_sottoconto = $sottoconto;
	}
	
	function set_des_sottoconto($des_sottoconto) {
		self::$_des_sottoconto = $des_sottoconto;
	}
	
	
}