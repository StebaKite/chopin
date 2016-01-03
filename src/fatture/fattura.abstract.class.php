<?php

require_once 'chopin.abstract.class.php';

abstract class FatturaAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

 	public static $queryRicercaClienti = "/fatture/ricercaClienti.sql";
 	public static $queryRicercaNumeroFattura = "/fatture/ricercaNumeroFattura.sql";

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

	/**
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $categoriaCliente
	 * @return string
	 */
	public function caricaClientiFatturabili($utility, $db, $categoriaCliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_cliente%' => trim($categoriaCliente)
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaClienti;		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		foreach(pg_fetch_all($result) as $row) {
			
			if ($row['id_cliente'] == $_SESSION["idcliente"]) {
				$elencoClienti .= "<option value='" . $row['id_cliente'] . "' selected >" . $row['des_cliente'] . "</option>";
			}
			else {
				$elencoClienti .= "<option value='" . $row['id_cliente'] . "'>" . $row['des_cliente'] . "</option>";
			}
		}
		return $elencoClienti;
	}
	
	public function caricaNumeroFattura($utility, $db, $categoriaCliente, $codiceNegozio) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_cliente%' => trim($categoriaCliente),
				'%neg_progr%' => trim($codiceNegozio)
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaNumeroFattura;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		foreach(pg_fetch_all($result) as $row) {
			$numeroFattura = $row['num_fattura_ultimo'];
		}
		return $numeroFattura;
	}
}

?>