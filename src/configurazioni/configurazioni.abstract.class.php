<?php

require_once 'chopin.abstract.class.php';

abstract class ConfigurazioniAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryCreaConto = "/configurazioni/creaConto.sql";
	public static $queryCreaSottoconto = "/configurazioni/creaSottoconto.sql";
	
	
	
	function __construct() {
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new ConfigurazioniAbstract();

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

	/**
	 * Questo metodo permette di inserire un conto in tabella
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 * @param unknown $desconto
	 * @param unknown $catconto
	 * @param unknown $tipconto
	 * @return unknown
	 */
	public function inserisciConto($db, $utility, $codconto, $desconto, $catconto, $tipconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%des_conto%' => trim($desconto),
				'%cat_conto%' => trim($catconto),
				'%tip_conto%' => trim($tipconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function inserisciSottoconto($db, $utility, $codconto, $codsottoconto, $dessottoconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%cod_sottoconto%' => trim($codsottoconto),
				'%des_sottoconto%' => trim($dessottoconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaSottoconto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
		
	
	
	
}
	
?>