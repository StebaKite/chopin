<?php

require_once 'chopin.abstract.class.php';

abstract class FatturaAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

 	public static $queryRicercaClienti = "/fatture/ricercaClienti.sql";
 	public static $queryRicercaNumeroFattura = "/fatture/ricercaNumeroFattura.sql";
 	public static $queryRicercaDatiCliente = "/fatture/ricercaDatiCliente.sql";

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
	 * Questo metodo carica tutti i clienti fatturabili di una certa categoria 
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
	
	/**
	 * Questo metodo preleva l'ultimo progressivo fattura utilizzato
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $categoriaCliente
	 * @param unknown $codiceNegozio
	 * @return un progressivo fattura utilizzabile
	 */
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
	
	/**
	 * Questo metodo preleva il tipo addebito di un cliente
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $idcliente
	 */
	public function caricaTipoAddebitoCliente($utility, $db, $idcliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente),
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaDatiCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		foreach(pg_fetch_all($result) as $row) {
			$tipoAddebito = trim($row['tip_addebito']);
			$_SESSION["descliente"] = trim($row['des_cliente']);
			$_SESSION["indirizzocliente"] = trim($row['des_indirizzo_cliente']);
			$_SESSION["cittacliente"] = trim($row['des_citta_cliente']);
			$_SESSION["capcliente"] = trim($row['cap_cliente']);
			$_SESSION["pivacliente"] = trim($row['cod_piva']);
			$_SESSION["cfiscliente"] = trim($row['cod_fisc']);
		}
		return $tipoAddebito;		
	}
	
}

?>