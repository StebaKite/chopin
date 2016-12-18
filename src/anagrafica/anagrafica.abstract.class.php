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
	public static $queryLeggiUltimoCodiceCliente = "/anagrafica/leggiUltimoCodiceCliente.sql";
	public static $queryLeggiUltimoCodiceFornitore = "/anagrafica/leggiUltimoCodiceFornitore.sql";

	public static $queryCreaMercato = "/anagrafica/creaMercato.sql";
	public static $queryDeleteMercato = "/anagrafica/deleteMercato.sql";
	public static $queryUpdateMercato = "/anagrafica/updateMercato.sql";
	
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
	public function cercaPartivaIvaCliente($db, $utility, $codpiva, $idcliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_piva%' => trim($codpiva),
				'%id_cliente%' => trim($idcliente)
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
				'%cod_fisc%' => trim($codfisc),
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
		
		/**
		 * Creo anche il conto per il fornitore
		 */
		
		$result = $this->inserisciSottoconto($db, $utility, '215', $codfornitore, $desfornitore);
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

		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiIdFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		/**
		 * Cancello il conto del fornitore
		 * @var array $conto
		 */
		$conto = explode(",", $array["fornitori"]);
		
		foreach(pg_fetch_all($result) as $row) {
		
			foreach ($conto as $contoFornitori) {
				$this->cancellaSottoconto($db, $utility, $contoFornitori, $row['cod_fornitore']);
			}
		}
		
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
		
		/**
		 * Creo anche il conto per il cliente
		 */
		
		$result = $this->inserisciSottoconto($db, $utility, '120', $codcliente, $descliente);
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

		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiIdCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		/**
		 * Cancello il conto del cliente
		 * @var array $conto
		 */
		$conto = explode(",", $array["clienti"]);
		
		foreach(pg_fetch_all($result) as $row) {

			foreach ($conto as $contoClienti) {				
				$this->cancellaSottoconto($db, $utility, $contoClienti, $row['cod_cliente']);
			}
		}
		
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

	/**
	 * Questo metodo crea un nuovo mercato
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codmercato
	 * @param unknown $desmercato
	 * @param unknown $cittamercato
	 */
	public function inserisciMercato($db, $utility, $codmercato, $desmercato, $cittamercato, $codneg) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_mercato%' => trim($codmercato),
				'%des_mercato%' => trim($desmercato),
				'%citta_mercato%' => trim($cittamercato),
				'%cod_negozio%' => trim($codneg)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaMercato;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}
	
	/**
	 * Questo metodo cancella un mercato
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idmercato
	 */
	public function cancellaMercato($db, $utility, $idmercato) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_mercato%' => trim($idmercato)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteMercato;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);		
	}
	
	public function updateMercato($db, $utility, $idmercato, $codmercato, $desmercato, $cittamercato, $codneg) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_mercato%' => trim($idmercato),
				'%cod_mercato%' => trim($codmercato),
				'%des_mercato%' => trim($desmercato),
				'%citta_mercato%' => trim($cittamercato),
				'%cod_negozio%' => trim($codneg),
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateMercato;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;		
	}
}
	
?>