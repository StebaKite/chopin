<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Fornitore implements CoreInterface {
	
	public $root;
	
	// Nomi colonne tabella Fornitore
	
	const ID_FORNITORE = "id_fornitore";
	const COD_FORNITORE = "cod_fornitore";
	const DES_FORNITORE = "des_fornitore";
	const DES_INDIRIZZO_FORNITORE = "des_indirizzo_fornitore";
	const DES_CITTA_FORNITORE = "des_citta_fornitore";
	const CAP_FORNITORE = "cap_fornitore";
	const TIP_ADDEBITO = "tip_addebito";
	const DAT_CREAZIONE = "dat_creazione";
	const NUM_GG_SCADENZA_FATTURA = "num_gg_scadenza_fattura";
	
	// Dati fornitore
	
	public $id_fornitore;
	public $cod_fornitore;
	public $des_fornitore;
	public $des_indirizzo_fornitore;
	public $des_citta_fornitore;
	public $cap_fornitore;
	public $tip_addebito;
	public $dat_creazione;
	public $num_gg_scadenza_fattura;
	
	// Queries
	
	public static $queryLeggiUltimoCodiceFornitore = "/anagrafica/leggiUltimoCodiceFornitore.sql";
	public static $queryLeggiFornitore = "/anagrafica/ricercaCodiceFornitore.sql";
	public static $queryCreaFornitore = "/anagrafica/creaFornitore.sql";
	public static $queryDeleteFornitore = "/anagrafica/deleteFornitore.sql";
	public static $queryUpdateFornitore = "/anagrafica/updateFornitore.sql";
	
	
	function __construct() {
		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance() {
	
		if (!isset($_SESSION[self::FORNITORE])) $_SESSION[self::FORNITORE] = serialize(new Fornitore());
		return unserialize($_SESSION[self::FORNITORE]);
	}

	public function preparaNuovoFornitore() {
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$this->set_cod_fornitore($this->prelevaUltimoCodiceFornitore($utility, $db) + 1);
		$this->set_des_fornitore(null);
		$this->set_des_indirizzo_fornitore(null);
		$this->set_des_citta_fornitore(null);
		$this->set_cap_fornitore(null);
	}

	public function inserisciFornitore($db) {

		$utility = Utility::getInstance();
		
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_fornitore%' => $this->get_cod_fornitore(),
				'%des_fornitore%' => $this->get_des_fornitore(),
				'%des_indirizzo_fornitore%' => $this->get_des_indirizzo_fornitore(),
				'%des_citta_fornitore%' => $this->get_des_citta_fornitore(),
				'%cap_fornitore%' => $this->get_cap_fornitore(),
				'%tip_addebito%' => $this->get_tip_addebito(),
				'%num_gg_scadenza_fattura%' => $this->get_num_gg_scadenza_fattura()
		);
		$sqlTemplate = $this->root . $array['query'] . self::$queryCreaFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		/**
		 * Creo anche il conto per il fornitore.
		 * Qui devi creare la classe Sottoconto nel package _core_
		 */
			
// 		if ($result) {
// 			$result = $this->inserisciSottoconto($db, $utility, '215', $codfornitore, $desfornitore);
// 		}
		return $result;
	}
	
	
	
	
	/**
	 * Questo metodo preleva l'ultimo codice fornitore utilizzato
	 * @param unknown $utility
	 * @param unknown $db
	 * @return unknown
	 */
	private function prelevaUltimoCodiceFornitore($utility, $db) {
	
		$array = $utility->getConfig();
		$sqlTemplate = $this->root . $array['query'] . self::$queryLeggiUltimoCodiceFornitore;
		$sql = $utility->getTemplate($sqlTemplate);
		$rows = pg_fetch_all($db->getData($sql));
	
		foreach($rows as $row) {
			$result = $row['cod_fornitore_ult'];
		}
		return $result;
	}
	
	
	
	

	/**
	 * Getters e setters
	 */
	
	public function set_id_fornitore($id_fornitore) {
		$this->id_fornitore = $id_fornitore;
	}
	public function get_id_fornitore() {
		return $this->id_fornitore;
	}
	
	public function set_cod_fornitore($cod_fornitore) {
		$this->cod_fornitore = $cod_fornitore;
	}
	public function get_cod_fornitore() {
		return $this->cod_fornitore;
	}
	
	public function set_des_fornitore($des_fornitore) {
		$this->des_fornitore = $des_fornitore;
	}
	public function get_des_fornitore() {
		return $this->des_fornitore;
	}

	public function set_des_indirizzo_fornitore($des_indirizzo_fornitore) {
		$this->des_indirizzo_fornitore = $des_indirizzo_fornitore;
	}
	public function get_des_indirizzo_fornitore() {
		return $this->des_indirizzo_fornitore;
	}

	public function set_des_citta_fornitore($des_citta_fornitore) {
		$this->des_citta_fornitore = $des_citta_fornitore;
	}
	public function get_des_citta_fornitore() {
		return $this->des_citta_fornitore;
	}

	public function set_cap_fornitore($cap_fornitore) {
		$this->cap_fornitore = $cap_fornitore;
	}
	public function get_cap_fornitore() {
		return $this->cap_fornitore;
	}

	public function set_tip_addebito($tip_addebito) {
		$this->tip_addebito = $tip_addebito;
	}
	public function get_tip_addebito() {
		return $this->tip_addebito;
	}

	public function set_dat_creazione($dat_creazione) {
		$this->dat_creazione = $dat_creazione;
	}
	public function get_dat_creazione() {
		return $this->dat_creazione;
	}

	public function set_num_gg_scadenza_fattura($num_gg_scadenza_fattura) {
		$this->num_gg_scadenza_fattura = $num_gg_scadenza_fattura;
	}
	public function get_num_gg_scadenza_fattura() {
		return $this->num_gg_scadenza_fattura;
	}
}

?>