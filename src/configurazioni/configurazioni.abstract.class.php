<?php

require_once 'chopin.abstract.class.php';

abstract class ConfigurazioniAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryCreaConto = "/configurazioni/creaConto.sql";
	public static $queryLeggiConto = "/configurazioni/leggiConto.sql";
	public static $queryLeggiSottoconti = "/configurazioni/leggiSottoconti.sql";
	public static $queryUpdateConto = "/configurazioni/updateConto.sql";
	public static $queryDeleteSottoconto = "/configurazioni/deleteSottoconto.sql";
	public static $queryDeleteConto = "/configurazioni/deleteConto.sql";
	public static $queryCreaCausale = "/configurazioni/creaCausale.sql";
	public static $queryLeggiCausale = "/configurazioni/leggiCausale.sql";
	public static $queryUpdateCausale = "/configurazioni/updateCausale.sql";
	public static $queryDeleteCausale = "/configurazioni/deleteCausale.sql";	
	public static $queryLeggiContiCausale = "/configurazioni/leggiContiCausale.sql";
	public static $queryLeggiContiDisponibili = "/configurazioni/leggiContiDisponibili.sql";
	public static $queryCreaConfigurazioneCausale = "/configurazioni/creaConfigurazioneCausale.sql";
	public static $queryDeleteConfigurazioneCausale = "/configurazioni/deleteConfigurazioneCausale.sql";
	
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
	public function inserisciConto($db, $utility, $codconto, $desconto, $catconto, $tipconto, $indpresenza, $indvisibilitasottoconti, $numrigabilancio) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%des_conto%' => trim($desconto),
				'%cat_conto%' => trim($catconto),
				'%tip_conto%' => trim($tipconto),
				'%ind_presenza_in_bilancio%' => trim($indpresenza),
				'%ind_visibilita_sottoconti%' => trim($indvisibilitasottoconti),
				'%num_riga_bilancio%' => trim($numrigabilancio)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo legge un conto
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 * @return unknown
	 */
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

	/**
	 * Questo metodo legge un sottoconto
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 * @return unknown
	 */
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

	/**
	 * Questo metodo aggiorna un conto
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 * @param unknown $desconto
	 * @param unknown $catconto
	 * @param unknown $tipconto
	 * @param unknown $indpresenza
	 * @param unknown $indvisibilitasottoconti
	 * @param unknown $numrigabilancio
	 * @return unknown
	 */
	public function updateConto($db, $utility, $codconto, $desconto, $catconto, $tipconto, $indpresenza, $indvisibilitasottoconti, $numrigabilancio) {
		
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%des_conto%' => trim($desconto),
				'%cat_conto%' => trim($catconto),
				'%tip_conto%' => trim($tipconto),
				'%ind_presenza_in_bilancio%' => trim($indpresenza),
				'%ind_visibilita_sottoconti%' => trim($indvisibilitasottoconti),
				'%num_riga_bilancio%' => trim($numrigabilancio)				
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo cancella un sottoconto
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 * @param unknown $codsottoconto
	 */
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
	
	/**
	 * Questo metodo cancella un conto
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 */
	public function cancellaConto($db, $utility, $codconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	/**
	 * Questo metodo inserisce una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $descausale
	 * @param unknown $catcausale
	 * @return unknown
	 */
	public function inserisciCausale($db, $utility, $codcausale, $descausale, $catcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%des_causale%' => trim($descausale),
				'%cat_causale%' => trim($catcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
	/**
	 * Questo metodo legge una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @return unknown
	 */
	public function leggiCausale($db, $utility, $codcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
	
	/**
	 * Questo metodo aggiorna una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $descausale
	 * @param unknown $catcausale
	 * @return unknown
	 */
	public function updateCausale($db, $utility, $codcausale, $descausale, $catcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%des_causale%' => trim($descausale),
				'%cat_causale%' => trim($catcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo cancella una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 */
	public function cancellaCausale($db, $utility, $codcausale) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	/**
	 * Questo metodo legge tutti i conti configurati su una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @return unknown
	 */
	public function leggiContiCausale($db, $utility, $codcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiContiCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
	
	/**
	 * Questo metodo legge tutti i conti non ancrora associati alla causale corrente
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @return unknown
	 */
	public function leggiContiDisponibili($db, $utility, $codcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiContiDisponibili;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Questo metodo associa un conto ad una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $codconto
	 * @return unknown
	 */
	public function creaConfigurazioneCausale($db, $utility, $codcausale, $codconto) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaConfigurazioneCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
	/**
	 * Questo metodo toglie il conto dalla causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $codconto
	 * @return unknown
	 */
	public function deleteConfigurazioneCausale($db, $utility, $codcausale, $codconto) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteConfigurazioneCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}	
}
	
?>