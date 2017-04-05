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
	private $sottoconti;
	private $qtaSottoconti;	
	private $sottocontiInseriti;
	
	private $dataRegistrazioneDa;		// dati di filtro per la generazione del mastrino
	private $dataRegistrazioneA;
	private $codNegozio;
	private $saldiInclusi;
	private $registrazioniTrovate;
	private $qtaRegistrazioniTrovate;

	private $nuoviSottoconti = array();		// utilizzata per accumulare i sottoconti durante la creazione del conto
	
	// Queries
	
	const CREA_SOTTOCONTO     = "/configurazioni/creaSottoconto.sql";
	const CANCELLA_SOTTOCONTO = "/configurazioni/deleteSottoconto.sql";
	const LEGGI_SOTTOCONTI    = "/configurazioni/leggiSottoconti.sql";
	const RICERCA_REGISTRAZIONI_CONTO = "/configurazioni/ricercaRegistrazioniConto.sql";
	const RICERCA_REGISTRAZIONI_CONTO_SALDI = "/configurazioni/ricercaRegistrazioniContoConSaldi.sql";
	
	// Metodi
	
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
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SOTTOCONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		
		if ($result) {
			$this->leggi($db);		// refresh dei sottoconti caricati
			$_SESSION[self::SOTTOCONTO] = serialize($this);
		}
		return $result;
	}

	public function cancella($db) {
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_conto%' => $this->getCodConto(),
				'%cod_sottoconto%' => $this->getCodSottoconto()
		);
		$sqlTemplate = $this->root . $array['query'] . self::CANCELLA_SOTTOCONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);		
		
		if ($db->getData($sql)) {
			$this->leggi($db);		// refresh dei sottoconti caricati
			$_SESSION[self::SOTTOCONTO] = serialize($this);
		}
	}	
	
	public function leggi($db) {
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%cod_conto%' => $this->getCodConto()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_SOTTOCONTI;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		if ($result) {
			$this->setSottoconti(pg_fetch_all($result));
			$this->setQtaSottoconti(pg_num_rows($result));
		}
		else {
			$this->setSottoconti(null);
			$this->setQtaSottoconti(null);
		}
	}
	
	public function cercaRegistrazioni($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$filtro = "";
		$filtroSaldo = "";
		
		if (($this->getDataRegistrazioneDa() != "") & ($this->getDataRegistrazioneA() != "")) {
			$filtro .= "and registrazione.dat_registrazione between '" . $this->getDataRegistrazioneDa() . "' and '" . $this->getDataRegistrazioneA() . "'" ;
			$filtroSaldo .= "and saldo.dat_saldo = '" . $this->getDataRegistrazioneDa() . "'" ;
		}
		
		if ($this->getCodNegozio() != "") {
			$filtro .= " and registrazione.cod_negozio = '" . $this->getCodNegozio() . "'" ;
			$filtroSaldo .= " and saldo.cod_negozio = '" . $this->getCodNegozio() . "'" ;
		}
		
		$replace = array(
				'%cod_conto%' => trim($this->getCodConto()),
				'%cod_sottoconto%' => trim($this->getCodSottoconto()),
				'%filtro_date%' => $filtro,
				'%filtro_date_saldo%' => $filtroSaldo
		);
		
		if ($this->getSaldiInclusi() == "S") {
			$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_REGISTRAZIONI_CONTO_SALDI;
		}
		else {
			$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_REGISTRAZIONI_CONTO;
		}
		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		
		$result = $db->getData($sql);
		
		if (pg_num_rows($result) > 0) {
			$this->setRegistrazioniTrovate(pg_fetch_all($result));
			$this->setQtaRegistrazioniTrovate(pg_num_rows($result));
		}
		else {
			$this->setRegistrazioniTrovate(null);
			$this->setQtaRegistrazioniTrovate(0);
		}
		return $result;
	}
	
	public function aggiungiNuovoSottoconto() {
		$item = array($this->getCodSottoconto(), $this->getDesSottoconto());		
		array_push($this->nuoviSottoconti, $item);
		sort($this->nuoviSottoconti);		
	}
	
	public function togliNuovoSottoconto() {

		$nuoviSottocontiDiff = array();

		foreach ($this->getNuoviSottoconti() as $unSottoconto) {
			if ($unSottoconto[0] != $this->getCodSottoconto()) {
				array_push($nuoviSottocontiDiff, $unSottoconto);
			}			
		}		
		$this->setNuoviSottoconti($nuoviSottocontiDiff);
	}

	public function preparaNuoviSottoconti() {
		$this->setNuoviSottoconti(array());		
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

    public function getSottoconti(){
        return $this->sottoconti;
    }

    public function setSottoconti($sottoconti){
        $this->sottoconti = $sottoconti;
    }

    public function getQtaSottoconti(){
        return $this->qtaSottoconti;
    }

    public function setQtaSottoconti($qtaSottoconti){
        $this->qtaSottoconti = $qtaSottoconti;
    }

    public function getSottocontiInseriti(){
        return $this->sottocontiInseriti;
    }

    public function setSottocontiInseriti($sottocontiInseriti){
        $this->sottocontiInseriti = $sottocontiInseriti;
    }


    public function getDataRegistrazioneDa(){
        return $this->dataRegistrazioneDa;
    }

    public function setDataRegistrazioneDa($dataRegistrazioneDa){
        $this->dataRegistrazioneDa = $dataRegistrazioneDa;
    }

    public function getDataRegistrazioneA(){
        return $this->dataRegistrazioneA;
    }

    public function setDataRegistrazioneA($dataRegistrazioneA){
        $this->dataRegistrazioneA = $dataRegistrazioneA;
    }

    public function getCodNegozio(){
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio){
        $this->codNegozio = $codNegozio;
    }

    public function getSaldiInclusi(){
        return $this->saldiInclusi;
    }

    public function setSaldiInclusi($saldiInclusi){
        $this->saldiInclusi = $saldiInclusi;
    }


    public function getRegistrazioniTrovate(){
        return $this->registrazioniTrovate;
    }

    public function setRegistrazioniTrovate($registrazioniTrovate){
        $this->registrazioniTrovate = $registrazioniTrovate;
    }

    public function getQtaRegistrazioniTrovate(){
        return $this->qtaRegistrazioniTrovate;
    }

    public function setQtaRegistrazioniTrovate($qtaRegistrazioniTrovate){
        $this->qtaRegistrazioniTrovate = $qtaRegistrazioniTrovate;
    }


    public function getNuoviSottoconti(){
        return $this->nuoviSottoconti;
    }

    public function setNuoviSottoconti($nuoviSottoconti){
        $this->nuoviSottoconti = $nuoviSottoconti;
    }

}

?>