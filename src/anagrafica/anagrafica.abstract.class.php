<?php

require_once 'chopin.abstract.class.php';

abstract class AnagraficaAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------
	
	public static $queryLeggiFornitore = "/anagrafica/leggiFornitore.sql";
	public static $queryCreaFornitore = "/anagrafica/creaFornitore.sql";
	
	
	
	function __construct() {
	}
	
	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new AnagraficaAbstract();
	
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
	
	public function leggiFornitori($db, $utility, $codfornitore) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_fornitore%' => trim($codfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	public function inserisciFornitore($db, $utility, $codfornitore, $desfornitore, $indfornitore, $cittafornitore, $capfornitore, $tipoaddebito) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_fornitore%' => trim($codfornitore),
				'%des_fornitore%' => trim($desfornitore),
				'%des_indirizzo_fornitore%' => trim($indfornitore),
				'%des_citta_fornitore%' => trim($cittafornitore),
				'%cap_fornitore%' => trim($capfornitore),
				'%tip_addebito%' => trim($tipoaddebito)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
		
		
		
	}
	
	
	
}
	
?>