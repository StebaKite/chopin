<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Sottoconto implements CoreInterface {
	
	private $root;

	// Nomi colonne tabella Sottoconto
	
	const COD_CONTO = "cod_conto";
	const COD_SOTTOCONTO = "cod_sottoconto";
	const DES_SOTTOCONTO = "des_sottoconto";
	const DAT_CREAZIONE_SOTTOCONTO = "dat_creazione_sottoconto";
	const IND_GRUPPO = "ind_gruppo";

	// dati sottoconto
	
	private $cod_conto;
	private $cod_sottoconto;
	private $des_sottoconto;
	private $dat_creazione_sottoconto;
	private $ind_gruppo;
	
	// Queries
	
	private static $queryCreaSottoconto = "/configurazioni/creaSottoconto.sql";
	
	
	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}
	
	public function getInstance() {
	
		if (!isset($_SESSION[self::SOTTOCONTO])) $_SESSION[self::SOTTOCONTO] = serialize(new Sottoconto());
		return unserialize($_SESSION[self::SOTTOCONTO]);
	}

	public function inserisci($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_conto%' => $this->getCodConto(),
				'%cod_sottoconto%' => $this->getCodSottoconto(),
				'%des_sottoconto%' => $this->getDesSottoconto()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::$queryCreaSottoconto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
		
	/************************************************************************
	 * Getters e setters
	 */

	public function setRoot($root) {
		$this->root = $root;
	}
	public function getRoot() {
		return $this->root;
	}
	
	public function setCodConto($cod_conto) {
		$this->cod_conto = $cod_conto;
	}
	public function getCodConto() {
		return $this->cod_conto;
	}

	public function setCodSottoconto($cod_sottoconto) {
		$this->cod_sottoconto = $cod_sottoconto;
	}
	public function getCodSottoconto() {
		return $this->cod_sottoconto;
	}

	public function setDesSottoconto($des_sottoconto) {
		$this->des_sottoconto = $des_sottoconto;
	}
	public function getDesSottoconto() {
		return $this->des_sottoconto;
	}

	public function setDatCreazioneSottoconto($dat_creazione_sottoconto) {
		$this->dat_creazione_sottoconto = $dat_creazione_sottoconto;
	}
	public function getDatCreazioneSottoconto() {
		return $this->dat_creazione_sottoconto;
	}

	public function setIndGruppo($ind_gruppo) {
		$this->ind_gruppo = $ind_gruppo;
	}
	public function getIndGruppo() {
		return $this->ind_gruppo;
	}
}

?>