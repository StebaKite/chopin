<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'sottoconto.class.php';

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

	private $id_fornitore;
	private $cod_fornitore;
	private $des_fornitore;
	private $des_indirizzo_fornitore;
	private $des_citta_fornitore;
	private $cap_fornitore;
	private $num_gg_scadenza_fattura;
	private $tip_addebito;
	private $dat_creazione;

	// Queries

	const ULTIMO_CODICE_FORNITORE = "/anagrafica/leggiUltimoCodiceFornitore.sql";
	const LEGGI_FORNITORE = "/anagrafica/ricercaCodiceFornitore.sql";
	const CREA_FORNITORE = "/anagrafica/creaFornitore.sql";
	const CANCELLA_FORNITORE = "/anagrafica/deleteFornitore.sql";
	const AGGIORNA_FORNITORE = "/anagrafica/updateFornitore.sql";

	// Metodi

	function __construct() {
		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance() {

		if (!isset($_SESSION[self::FORNITORE])) $_SESSION[self::FORNITORE] = serialize(new Fornitore());
		return unserialize($_SESSION[self::FORNITORE]);
	}

	public function prepara() {

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$this->set_cod_fornitore($this->prelevaUltimoCodice($utility, $db) + 1);
		$this->set_des_fornitore(null);
		$this->set_des_indirizzo_fornitore(null);
		$this->set_des_citta_fornitore(null);
		$this->set_cap_fornitore(null);
	}

	public function inserisci($db) {

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
		$sqlTemplate = $this->root . $array['query'] . self::CREA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		/**
		 * Creo anche il conto per il fornitore.
		 */

		if ($result) {
			$sottoconto = Sottoconto::getInstance();
			$sottoconto->set_cod_conto($array["contoFornitoreNazionale"]);
			$sottoconto->set_cod_sottoconto($this->get_cod_fornitore());
			$sottoconto->set_des_sottoconto($this->get_des_fornitore());

			$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
			$result = $sottoconto->inserisci($db);
		}
		return $result;
	}

	private function prelevaUltimoCodice($utility, $db) {

		$array = $utility->getConfig();
		$sqlTemplate = $this->root . $array['query'] . self::ULTIMO_CODICE_FORNITORE;
		$sql = $utility->getTemplate($sqlTemplate);
		$rows = pg_fetch_all($db->getData($sql));

		foreach($rows as $row) {
			$result = $row['cod_fornitore_ult'];
		}
		return $result;
	}





	/************************************************************************
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
