<?php

require_once 'chopin.abstract.class.php';

abstract class AnagraficaAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------
	
	public static $queryLeggiFornitore = "/anagrafica/ricercaCodiceFornitore.sql";
	public static $queryLeggiPivaCliente = "/anagrafica/ricercaPivaCliente.sql";
	public static $queryLeggiCfisCliente = "/anagrafica/ricercaCfisCliente.sql";
	public static $queryCreaFornitore = "/anagrafica/creaFornitore.sql";
	public static $queryDeleteFornitore = "/anagrafica/deleteFornitore.sql";
	public static $queryUpdateFornitore = "/anagrafica/updateFornitore.sql";

	public static $queryCreaCliente = "/anagrafica/creaCliente.sql";
	public static $queryUpdateCliente = "/anagrafica/updateCliente.sql";
	public static $queryDeleteCliente = "/anagrafica/deleteCliente.sql";
	public static $queryRicercacCategorie = "/anagrafica/leggiCategorieCliente.sql";
	public static $queryLeggiUltimoCodiceCliente = "/anagrafica/leggiUltimoCodiceCliente.sql";
	public static $queryLeggiUltimoCodiceFornitore = "/anagrafica/leggiUltimoCodiceFornitore.sql";
	
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
	
	// Metodi comuni di utilita della prima nota ---------------------------
	
	/**
	 * Questo metodo cerca un fornitore tramite il codice fornitore
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codfornitore
	 * @return unknown
	 */
	public function cercaCodiceFornitore($db, $utility, $codfornitore) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_fornitore%' => trim($codfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Questo metodo cerca un cliente tramite il suo codice di partita iva
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codpiva
	 * @return unknown
	 */
	public function cercaPartivaIvaCliente($db, $utility, $codpiva) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_piva%' => trim($codpiva)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiPivaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;		
	}

	/**
	 * Questo metodo cerca un cliente tramite il suo codice fiscale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codfisc
	 * @return unknown
	 */
	public function cercaCodiceFiscaleCliente($db, $utility, $codfisc) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_fisc%' => trim($codfisc)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiCfisCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
	
	/**
	 * Questo metodo inserisce un fornitore
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codfornitore
	 * @param unknown $desfornitore
	 * @param unknown $indfornitore
	 * @param unknown $cittafornitore
	 * @param unknown $capfornitore
	 * @param unknown $tipoaddebito
	 * @param unknown $numggscadenzafattura
	 * @return unknown
	 */
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

	/**
	 * Questo metodo cancella un fornitore tramite il suo ID
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 */
	public function cancellaFornitore($db, $utility, $idfornitore) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	/**
	 * Questo metodo aggiorna i dati del fornitore
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @param unknown $codfornitore
	 * @param unknown $desfornitore
	 * @param unknown $indfornitore
	 * @param unknown $cittafornitore
	 * @param unknown $capfornitore
	 * @param unknown $tipoaddebito
	 * @param unknown $numggscadenzafattura
	 * @return unknown
	 */
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

	/**
	 * Questo metodo inserisce un cliente
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcliente
	 * @param unknown $descliente
	 * @param unknown $indcliente
	 * @param unknown $cittacliente
	 * @param unknown $capcliente
	 * @param unknown $tipoaddebito
	 * @param unknown $codpiva
	 * @param unknown $codfisc
	 * @param unknown $catcliente
	 * @return unknown
	 */
	public function inserisciCliente($db, $utility, $codcliente, $descliente, $indcliente, $cittacliente, $capcliente, $tipoaddebito, $codpiva, $codfisc, $catcliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_cliente%' => trim($codcliente),
				'%des_cliente%' => trim($descliente),
				'%des_indirizzo_cliente%' => trim($indcliente),
				'%des_citta_cliente%' => trim($cittacliente),
				'%cap_cliente%' => trim($capcliente),
				'%tip_addebito%' => trim($tipoaddebito),
				'%cod_piva%' => trim($codpiva),
				'%cod_fisc%' => trim($codfisc),
				'%cat_cliente%' => trim($catcliente)	
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Il metodo cancella un cliente tramite il suo ID
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idcliente
	 */
	public function cancellaCliente($db, $utility, $idcliente) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}
	
	/**
	 * Il metodo aggiorna i dati del cliente
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idcliente
	 * @param unknown $codcliente
	 * @param unknown $descliente
	 * @param unknown $indcliente
	 * @param unknown $cittacliente
	 * @param unknown $capcliente
	 * @param unknown $tipoaddebito
	 * @param unknown $codpiva
	 * @param unknown $codfisc
	 * @param unknown $catcliente
	 * @return unknown
	 */
	public function updateCliente($db, $utility, $idcliente, $codcliente, $descliente, $indcliente, $cittacliente, $capcliente, $tipoaddebito, $codpiva, $codfisc, $catcliente) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente),
				'%cod_cliente%' => trim($codcliente),
				'%des_cliente%' => trim($descliente),
				'%des_indirizzo_cliente%' => trim($indcliente),
				'%des_citta_cliente%' => trim($cittacliente),
				'%cap_cliente%' => trim($capcliente),
				'%tip_addebito%' => trim($tipoaddebito),
				'%cod_piva%' => trim($codpiva),
				'%cod_fisc%' => trim($codfisc),
				'%cat_cliente%' => trim($catcliente)	
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo legge tutte le categorie disponibili
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaCategorieCliente($utility, $db) {

		$array = $utility->getConfig();
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercacCategorie;
		$sql = $utility->getTemplate($sqlTemplate);
		$result = $db->getData($sql);
		
		foreach(pg_fetch_all($result) as $row) {
		
			if ($row['cat_cliente'] == $_SESSION["catcliente"]) {
				$elencoCategorieCliente .= "<option value='" . $row['cat_cliente'] . "' selected >" . $row['des_categoria'] . "</option>";
			}
			else {
				$elencoCategorieCliente .= "<option value='" . $row['cat_cliente'] . "'>" . $row['des_categoria'] . "</option>";
			}
		}
		return $elencoCategorieCliente;
	}
	
	/**
	 * Questo metodo preleva l'ultimo codoce cliente utilizzato
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function prelevaUltimoCodiceCliente($utility, $db) {

		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiUltimoCodiceCliente;
		$sql = $utility->getTemplate($sqlTemplate);		
		$rows = pg_fetch_all($db->getData($sql));
				
		foreach($rows as $row) {
			$result = $row['cod_cliente_ult'];
		}
		return $result;
	}
	
	/**
	 * Questo metodo preleva l'ultimo codice fornitore utilizzato
	 * @param unknown $utility
	 * @param unknown $db
	 * @return unknown
	 */
	public function prelevaUltimoCodiceFornitore($utility, $db) {

		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiUltimoCodiceFornitore;
		$sql = $utility->getTemplate($sqlTemplate);
		$rows = pg_fetch_all($db->getData($sql));
		
		foreach($rows as $row) {
			$result = $row['cod_fornitore_ult'];
		}
		return $result;
		
	}	
}
	
?>