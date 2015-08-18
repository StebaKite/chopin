<?php

require_once 'chopin.abstract.class.php';

abstract class ConfigurazioniAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryCreaConto = "/configurazioni/creaConto.sql";
	public static $queryCreaSottoconto = "/configurazioni/creaSottoconto.sql";
	public static $queryLeggiConto = "/configurazioni/leggiConto.sql";
	public static $queryLeggiSottoconti = "/configurazioni/leggiSottoconti.sql";
	public static $queryUpdateConto = "/configurazioni/updateConto.sql";
	public static $queryDeleteSottoconto = "/configurazioni/deleteSottoconto.sql";
	public static $queryDeleteConto = "/configurazioni/deleteConto.sql";
	
	
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

	public function leggiConto($db, $utility, $codconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	public function leggiSottoconti($db, $utility, $codconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiSottoconti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
	
	public function updateConto($db, $utility, $codconto, $desconto, $catconto, $tipconto) {
		
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%des_conto%' => trim($desconto),
				'%cat_conto%' => trim($catconto),
				'%tip_conto%' => trim($tipconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
	public function cancellaSottoconto($db, $utility, $codconto, $codsottoconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%cod_sottoconto%' => trim($codsottoconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteSottoconto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}
	
	public function cancellaConto($db, $utility, $codconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}
	
	
	
}
	
?>