<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Conto implements CoreInterface {

	private $root;

	// Nomi colonne tabella Sottoconto

	const COD_CONTO = "cod_conto";
	const DES_CONTO = "des_conto";
	const CAT_CONTO = "cat_conto";
	const TIP_CONTO = "tip_conto";
	const DAT_CREAZIONE_CONTO = "dat_creazione_conto";
	const IND_PRESENZA_IN_BILANCIO = "ind_presenza_in_bilancio";
	const NUM_RIGA_BILANCIO = "num_riga_bilancio";
	const IND_VISIBILITA_SOTTOCONTI = "ind_visibilita_sottoconti";

	// dati sottoconto

	private $codConto;
	private $desConto;
	private $catConto;
	private $tipConto;
	private $datCreazioneConto;
	private $indPresenzaInBilancio;
	private $numRigaBilancio;
	private $indVisibilitaSottoconti;
	private $conti;
	private $qtaConti;
	private $catContoSel;
	private $tipContoSel;

	// Queries

	const RICERCA_CONTO		= "/configurazioni/ricercaConto.sql";
	const LEGGI_CONTO		= "/configurazioni/leggiConto.sql";
	const AGGIORNA_CONTO	= "/configurazioni/updateConto.sql";
	const INSERISCI_CONTO	= "/configurazioni/creaConto.sql";
	const CANCELLA_CONTO	= "/configurazioni/deleteConto.sql";
	
	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::CONTO])) $_SESSION[self::CONTO] = serialize(new Conto());
		return unserialize($_SESSION[self::CONTO]);
	}

	public function load($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%categoria%' => ($this->getCatContoSel() != "") ? "and cat_conto = '" . $this->getCatContoSel() . "'" : "",
				'%tipconto%' => ($this->getTipContoSel() != "") ? "and tip_conto = '" . $this->getTipContoSel() . "'" : ""
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_CONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setConti(pg_fetch_all($result));
			$this->setQtaConti(pg_num_rows($result));
		} else {
			$this->setConti(null);
			$this->setQtaConti(null);
		}
		return $result;
	}
	
	public function leggi($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_conto%' => $this->getCodConto()
		);
		
		$sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_CONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if ($result) {
	
			$conto = pg_fetch_all($result);
			$numConti = pg_num_rows($result);
			
			foreach ($conto as $row) {
	
				$this->setDesConto(trim($row[self::DES_CONTO]));
				$this->setCatConto(trim($row[self::CAT_CONTO]));
				$this->setTipConto(trim($row[self::TIP_CONTO]));
				$this->setIndPresenzaInBilancio(trim($row[self::IND_PRESENZA_IN_BILANCIO]));
				$this->setIndVisibilitaSottoconti(trim($row[self::IND_VISIBILITA_SOTTOCONTI]));
				$this->setNumRigaBilancio(trim($row[self::NUM_RIGA_BILANCIO]));
			}
		}
		return $numConti;
	}

	public function aggiorna($db) {
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_conto%' => $this->getCodConto(),
				'%des_conto%' => str_replace("'","''",$this->getDesConto()),
				'%cat_conto%' => $this->getCatConto(),
				'%tip_conto%' => $this->getTipConto(),
				'%ind_presenza_in_bilancio%' => $this->getIndPresenzaInBilancio(),
				'%ind_visibilita_sottoconti%' => $this->getIndVisibilitaSottoconti(),
				'%num_riga_bilancio%' => $this->getNumRigaBilancio()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_CONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		if ($result) {
			$this->load($db);	// refresh dei conti caricati
			$_SESSION[self::CONTO] = serialize($this);
		}
		
		return $result;
	}

	public function prepara() {
		
		$this->setCodConto(null);
		$this->setDesConto(null);
		$this->setCatConto(null);
		$this->setTipConto(null);
		$this->setDatCreazioneConto(null);
		$this->setIndPresenzaInBilancio(null);
		$this->setNumRigaBilancio(null);
		$this->setIndVisibilitaSottoconti(null);
		
		$_SESSION[self::CONTO] = serialize($this);
	}

	public function inserisci($db) {
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_conto%' => $this->getCodConto(),
				'%des_conto%' => str_replace("'", "''", $this->getDesConto()),
				'%cat_conto%' => $this->getCatConto(),
				'%tip_conto%' => $this->getTipConto(),
				'%ind_presenza_in_bilancio%' => $this->getIndPresenzaInBilancio(),
				'%ind_visibilita_sottoconti%' => $this->getIndVisibilitaSottoconti(),
				'%num_riga_bilancio%' => $this->getNumRigaBilancio()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::INSERISCI_CONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		if ($result) {
			$this->load($db);	// refresh dei conti caricati
			$_SESSION[self::CONTO] = serialize($this);
		}
		return $result;
	}

	public function cancella($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$replace = array(
				'%cod_conto%' => $this->getCodConto()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_CONTO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

		if ($db->getData($sql)) {
			$this->load($db);		// refresh dei conti caricati
			$_SESSION[self::CONTO] = serialize($this);
		}
	}
	
	// Getters eSetters

    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getCodConto(){
        return $this->codConto;
    }

    public function setCodConto($codConto){
        $this->codConto = $codConto;
    }

    public function getDesConto(){
        return $this->desConto;
    }

    public function setDesConto($desConto){
        $this->desConto = $desConto;
    }

    public function getCatConto(){
        return $this->catConto;
    }

    public function setCatConto($catConto){
        $this->catConto = $catConto;
    }

    public function getTipConto(){
        return $this->tipConto;
    }

    public function setTipConto($tipConto){
        $this->tipConto = $tipConto;
    }

    public function getDatCreazioneConto(){
        return $this->datCreazioneConto;
    }

    public function setDatCreazioneConto($datCreazioneConto){
        $this->datCreazioneConto = $datCreazioneConto;
    }

    public function getIndPresenzaInBilancio(){
        return $this->indPresenzaInBilancio;
    }

    public function setIndPresenzaInBilancio($indPresenzaInBilancio){
        $this->indPresenzaInBilancio = $indPresenzaInBilancio;
    }

    public function getNumRigaBilancio(){
        return $this->numRigaBilancio;
    }

    public function setNumRigaBilancio($numRigaBilancio){
        $this->numRigaBilancio = $numRigaBilancio;
    }

    public function getIndVisibilitaSottoconti(){
        return $this->indVisibilitaSottoconti;
    }

    public function setIndVisibilitaSottoconti($indVisibilitaSottoconti){
        $this->indVisibilitaSottoconti = $indVisibilitaSottoconti;
    }

    public function getConti(){
        return $this->conti;
    }

    public function setConti($conti){
        $this->conti = $conti;
    }

    public function getQtaConti(){
        return $this->qtaConti;
    }

    public function setQtaConti($qtaConti){
        $this->qtaConti = $qtaConti;
    }


    public function getCatContoSel(){
        return $this->catContoSel;
    }

    public function setCatContoSel($catContoSel){
        $this->catContoSel = $catContoSel;
        return $this;
    }

    public function getTipContoSel(){
        return $this->tipContoSel;
    }

    public function setTipContoSel($tipContoSel){
        $this->tipContoSel = $tipContoSel;
        return $this;
    }

}

?>
