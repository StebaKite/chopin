<?php

require_once 'chopin.abstract.class.php';

abstract class AnagraficaAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------
	
	public static $queryLeggiFornitore = "/anagrafica/leggiFornitore.sql";
	public static $queryCreaFornitore = "/anagrafica/creaFornitore.sql";
	public static $queryDeleteFornitore = "/anagrafica/deleteFornitore.sql";
	public static $queryUpdateFornitore = "/anagrafica/updateFornitore.sql";

	public static $queryCreaCliente = "/anagrafica/creaCliente.sql";
	public static $queryLeggiIdCliente = "/anagrafica/leggiIdCliente.sql";
	public static $queryUpdateCliente = "/anagrafica/updateCliente.sql";
	public static $queryDeleteCliente = "/anagrafica/deleteCliente.sql";
	
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

	public function inserisciFornitore($db, $utility, $codfornitore, $desfornitore, $indfornitore, $cittafornitore, $capfornitore, $tipoaddebito, $numggscadenzafattura) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_fornitore%' => trim($codfornitore),
				'%des_fornitore%' => trim($desfornitore),
				'%des_indirizzo_fornitore%' => trim($indfornitore),
				'%des_citta_fornitore%' => trim($cittafornitore),
				'%cap_fornitore%' => trim($capfornitore),
				'%tip_addebito%' => trim($tipoaddebito),
				'%num_gg_scadenza_fattura%' => trim ($numggscadenzafattura)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function cancellaFornitore($db, $utility, $idfornitore) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	public function updateFornitore($db, $utility, $idfornitore, $codfornitore, $desfornitore, $indfornitore, $cittafornitore, $capfornitore, $tipoaddebito, $numggscadenzafattura) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore),
				'%cod_fornitore%' => trim($codfornitore),
				'%des_fornitore%' => trim($desfornitore),
				'%des_indirizzo_fornitore%' => trim($indfornitore),
				'%des_citta_fornitore%' => trim($cittafornitore),
				'%cap_fornitore%' => trim($capfornitore),
				'%tip_addebito%' => trim($tipoaddebito),
				'%num_gg_scadenza_fattura%' => trim($numggscadenzafattura)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function inserisciCliente($db, $utility, $codcliente, $descliente, $indcliente, $cittacliente, $capcliente, $tipoaddebito) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_cliente%' => trim($codcliente),
				'%des_cliente%' => trim($descliente),
				'%des_indirizzo_cliente%' => trim($indcliente),
				'%des_citta_cliente%' => trim($cittacliente),
				'%cap_cliente%' => trim($capcliente),
				'%tip_addebito%' => trim($tipoaddebito)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function cancellaCliente($db, $utility, $idcliente) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}
	
	public function leggiIdCliente($db, $utility, $idcliente) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiIdCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
	
	public function updateCliente($db, $utility, $idcliente, $codcliente, $descliente, $indcliente, $cittacliente, $capcliente, $tipoaddebito) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente),
				'%cod_cliente%' => trim($codcliente),
				'%des_cliente%' => trim($descliente),
				'%des_indirizzo_cliente%' => trim($indcliente),
				'%des_citta_cliente%' => trim($cittacliente),
				'%cap_cliente%' => trim($capcliente),
				'%tip_addebito%' => trim($tipoaddebito)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
}
	
?>