<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class Fornitore implements CoreInterface {

	private $root;

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
	const QTA_REGISTRAZIONI_FORNITORE = "tot_registrazioni_fornitore";

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
	private $qtaRegistrazioniFornitore;

	// Altri dati funzionali

	private $fornitori;
	private $qtaFornitori;

	// Queries

	const ULTIMO_CODICE_FORNITORE = "/anagrafica/leggiUltimoCodiceFornitore.sql";
	const LEGGI_FORNITORE = "/anagrafica/ricercaCodiceFornitore.sql";
	const CREA_FORNITORE = "/anagrafica/creaFornitore.sql";
	const CANCELLA_FORNITORE = "/anagrafica/deleteFornitore.sql";
	const AGGIORNA_FORNITORE = "/anagrafica/updateFornitore.sql";
	const QUERY_RICERCA_FORNITORE = "/anagrafica/ricercaFornitore.sql";
	const LEGGI_FORNITORE_X_ID = "/anagrafica/leggiIdFornitore.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::FORNITORE])) $_SESSION[self::FORNITORE] = serialize(new Fornitore());
		return unserialize($_SESSION[self::FORNITORE]);
	}

	public function prepara() {

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$this->setCodFornitore($this->prelevaUltimoCodice($utility, $db) + 1);
		$this->setDesFornitore(null);
		$this->setDesIndirizzoFornitore(null);
		$this->setDesCittaFornitore(null);
		$this->setCapFornitore(null);
	}

	public function inserisci($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%cod_fornitore%' => $this->getCodFornitore(),
				'%des_fornitore%' => $this->getDesFornitore(),
				'%des_indirizzo_fornitore%' => $this->getDesIndirizzoFornitore(),
				'%des_citta_fornitore%' => $this->getDesCittaFornitore(),
				'%cap_fornitore%' => $this->getCapFornitore(),
				'%tip_addebito%' => $this->getTipAddebito(),
				'%num_gg_scadenza_fattura%' => $this->getNumGgScadenzaFattura()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		if ($result) {
			$this->load($db);	// refresh dei fornitori caricati
			$_SESSION[self::FORNITORE] = serialize($this);
		}

		/**
		 * Creo anche il conto per il fornitore.
		 */

		if ($result) {
			$sottoconto = Sottoconto::getInstance();
			$sottoconto->setCodConto($array["contoFornitoreNazionale"]);
			$sottoconto->setCodSottoconto($this->getCodFornitore());
			$sottoconto->setDesSottoconto($this->getDesFornitore());

			$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
			$result = $sottoconto->inserisci($db);

			$conto = Conto::getInstance();
			$conto->load($db);		// refresh dei conti caricati
			$_SESSION[self::CONTO] = serialize($conto);
		}
		return $result;
	}

	private function prelevaUltimoCodice($utility, $db) {

		$array = $utility->getConfig();
		$sqlTemplate =  $this->getRoot() . $array['query'] . self::ULTIMO_CODICE_FORNITORE;
		$sql = $utility->getTemplate($sqlTemplate);
		$rows = pg_fetch_all($db->getData($sql));

		foreach($rows as $row) {
			$result = $row['cod_fornitore_ult'];
		}
		return $result;
	}

	public function load($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$sqlTemplate = $this->getRoot() . $array['query'] . self::QUERY_RICERCA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setFornitori(pg_fetch_all($result));
			$this->setQtaFornitori(pg_num_rows($result));
		} else {
			$this->setFornitori(null);
			$this->setQtaFornitori(null);
		}
		return $result;
	}

	public function cancella($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_fornitore%' => $this->getIdFornitore()
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_FORNITORE_X_ID;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		/**
		 * Cancello il conto del fornitore
		 * @var array $conto
		 */
		$sottoconto = Sottoconto::getInstance();
		$conto = explode(",", $array["contiFornitore"]);

		foreach(pg_fetch_all($result) as $row) {

			foreach ($conto as $contoFornitori) {
				$sottoconto->setCodConto($contoFornitori);
				$sottoconto->setCodSottoconto($row['cod_fornitore']);
				$sottoconto->cancella($db);
			}
		}

		$sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		if ($db->getData($sql)) {
			$this->load($db);	// refresh dei fornitori caricati
			$_SESSION[self::FORNITORE] = serialize($this);
		}
	}

	public function leggi($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($this->getIdFornitore())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_FORNITORE_X_ID;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		foreach(pg_fetch_all($result) as $row) {
			$this->setCodFornitore($row[self::COD_FORNITORE]);
			$this->setDesFornitore($row[self::DES_FORNITORE]);
			$this->setDesIndirizzoFornitore($row[self::DES_INDIRIZZO_FORNITORE]);
			$this->setDesCittaFornitore($row[self::DES_CITTA_FORNITORE]);
			$this->setCapFornitore($row[self::CAP_FORNITORE]);
			$this->setTipAddebito($row[self::TIP_ADDEBITO]);
			$this->setDatCreazione($row[self::DAT_CREAZIONE]);
			$this->setNumGgScadenzaFattura($row[self::NUM_GG_SCADENZA_FATTURA]);
			$this->setQtaRegistrazioniFornitore($row[self::QTA_REGISTRAZIONI_FORNITORE]);
		}
		return $result;
	}

	public function update($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_fornitore%' => $this->getIdFornitore(),
				'%cod_fornitore%' => $this->getCodFornitore(),
				'%des_fornitore%' => $this->getDesFornitore(),
				'%des_indirizzo_fornitore%' => $this->getDesIndirizzoFornitore(),
				'%des_citta_fornitore%' => $this->getDesCittaFornitore(),
				'%cap_fornitore%' => $this->getCapFornitore(),
				'%tip_addebito%' => $this->getTipAddebito(),
				'%num_gg_scadenza_fattura%' => $this->getNumGgScadenzaFattura()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		if ($result) {
			$this->load($db);	// refresh dei clienti caricati
			$_SESSION[self::FORNITORE] = serialize($this);
		}
		return $result;
	}

	/************************************************************************
	 * Getters e setters
	 */

	public function getRoot() {
		return $this->root;
	}
	public function setRoot($root) {
		$this->root = $root;
	}

	public function setIdFornitore($id_fornitore) {
		$this->id_fornitore = $id_fornitore;
	}
	public function getIdFornitore() {
		return $this->id_fornitore;
	}

	public function setCodFornitore($cod_fornitore) {
		$this->cod_fornitore = $cod_fornitore;
	}
	public function getCodFornitore() {
		return $this->cod_fornitore;
	}

	public function setDesFornitore($des_fornitore) {
		$this->des_fornitore = $des_fornitore;
	}
	public function getDesFornitore() {
		return $this->des_fornitore;
	}

	public function setDesIndirizzoFornitore($des_indirizzo_fornitore) {
		$this->des_indirizzo_fornitore = $des_indirizzo_fornitore;
	}
	public function getDesIndirizzoFornitore() {
		return $this->des_indirizzo_fornitore;
	}

	public function setDesCittaFornitore($des_citta_fornitore) {
		$this->des_citta_fornitore = $des_citta_fornitore;
	}
	public function getDesCittaFornitore() {
		return $this->des_citta_fornitore;
	}

	public function setCapFornitore($cap_fornitore) {
		$this->cap_fornitore = $cap_fornitore;
	}
	public function getCapFornitore() {
		return $this->cap_fornitore;
	}

	public function setTipAddebito($tip_addebito) {
		$this->tip_addebito = $tip_addebito;
	}
	public function getTipAddebito() {
		return $this->tip_addebito;
	}

	public function setDatCreazione($dat_creazione) {
		$this->dat_creazione = $dat_creazione;
	}
	public function getDatCreazione() {
		return $this->dat_creazione;
	}

	public function setNumGgScadenzaFattura($num_gg_scadenza_fattura) {
		$this->num_gg_scadenza_fattura = $num_gg_scadenza_fattura;
	}
	public function getNumGgScadenzaFattura() {
		return $this->num_gg_scadenza_fattura;
	}

	public function getFornitori() {
		return $this->fornitori;
	}
	public function setFornitori($fornitori) {
		$this->fornitori = $fornitori;
	}

	public function getQtaFornitori() {
		return $this->qtaFornitori;
	}
	public function setQtaFornitori($qtaFornitori) {
		$this->qtaFornitori = $qtaFornitori;
	}

	public function getQtaRegistrazioniFornitore() {
		return $this->qtaRegistrazioniFornitore;
	}
	public function setQtaRegistrazioniFornitore($qtaRegistrazioniFornitore) {
		$this->qtaRegistrazioniFornitore = $qtaRegistrazioniFornitore;
	}

}

?>
