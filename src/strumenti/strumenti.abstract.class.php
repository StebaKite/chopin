<?php

require_once 'chopin.abstract.class.php';

abstract class StrumentiAbstract extends ChopinAbstract {

	private static $_instance = null;
	
	// Query --------------------------------------------------------------- 
	
	public static $queryRicercaRegistrazioniConto = "/strumenti/ricercaRegistrazioniConto.sql";
	
	function __construct() {
	}
	
	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new StrumentiAbstract();
	
		return self::$_instance;
	}
	
	/**
	 * Metodi comuni
	 */

	public function caricaRegistrazioniConto($utility, $db) {
		
		$filtriRegistrazione = "";
		$filtriDettaglio = "";
		
		if ($_SESSION["codneg_sel"] != "") {
			$filtriRegistrazione .= "and reg.cod_negozio = '" . $_SESSION["codneg_sel"] . "'";
		}

		if ($_SESSION["conto_sel"] != "") {
			
			$conto = split(" - ", $_SESSION["conto_sel"]); 
			
			$filtriDettaglio .= "and detreg.cod_conto = '" . $conto[0] . "'";
			$filtriDettaglio .= "and detreg.cod_sottoconto = '" . $conto[1] . "'";
		}
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%filtri-registrazione%' => $filtriRegistrazione,
				'%filtri-dettaglio%' => $filtriDettaglio,
		);
		
		$array = $utility->getConfig();		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaRegistrazioniConto;	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		
		$result = $db->getData($sql);
		
		return $result;
	}
	
	
}

?>
