<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Causale implements CoreInterface {

	private $root;

	// Nomi colonne tabella Causale

	const COD_CAUSALE = "cod_causale";
	const DES_CAUSALE = "des_causale";
	const DAT_INSERIMENTO = "dat_inserimento";
	const CAT_CAUSALE = "cat_causale";

	// dati causale

	private $codCausale;
	private $desCausale;
	private $datInserimento;
	private $catCausale;
	private $causali;
	private $qtaCausali;
	private $qtaRegistrazioniCausale;
	private $qtaContiCausale;
	private $contiCausale;

	// Queries

	const RICERCA_CAUSALE			= "/configurazioni/ricercaCausale.sql";
	const CREA_CAUSALE				= "/configurazioni/creaCausale.sql";
	const CANCELLA_CAUSALE			= "/configurazioni/deleteCausale.sql";
	const LEGGI_CAUSALE				= "/configurazioni/leggiCausale.sql";
	const AGGIORNA_CAUSALE 			= "/configurazioni/updateCausale.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::CAUSALE])) $_SESSION[self::CAUSALE] = serialize(new Causale());
		return unserialize($_SESSION[self::CAUSALE]);
	}

	public function load($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array();

		$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_CAUSALE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setCausali(pg_fetch_all($result));
			$this->setQtaCausali(pg_num_rows($result));
		} else {
			$this->setCausali(null);
			$this->setQtaCausali(null);
		}
		return $result;
	}

	public function prepara()
	{
		$this->setCodCausale("");
		$this->setDesCausale("");
		$this->setCatCausale("");
	}

	public function inserisci($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%cod_causale%' => trim($this->getCodCausale()),
				'%des_causale%' => trim($this->getDesCausale()),
				'%cat_causale%' => trim($this->getCatCausale())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_CAUSALE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$this->load($db);		// refresh delle causali caricate
			$_SESSION[self::CAUSALE] = serialize($this);
		}
		return $result;
	}

	public function cancella($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%cod_causale%' => trim($this->getCodCausale())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_CAUSALE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

		if ($db->getData($sql)) {
			$this->load($db);		// refresh delle causali caricate
			$_SESSION[self::CAUSALE] = serialize($this);
		}
	}

	public function aggiorna($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$this->setDesCausale(str_replace("'","''",$this->getDesCausale()));

		$replace = array(
				'%cod_causale%' => trim($this->getCodCausale()),
				'%des_causale%' => trim($this->getDesCausale()),
				'%cat_causale%' => trim($this->getCatCausale())
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_CAUSALE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$this->load($db);		// refresh delle causali caricate
			$_SESSION[self::CAUSALE] = serialize($this);
		}
		return $result;
	}

	public function leggi($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%cod_causale%' => trim($this->getCodCausale())
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_CAUSALE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$causale = pg_fetch_all($result);
			foreach ($causale as $row) {
				$this->setDesCausale(trim($row[self::DES_CAUSALE]));
				$this->setCatCausale(trim($row[self::CAT_CAUSALE]));
			}
		}
		return $result;
	}

	// Getters & Setters

    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getCodCausale(){
        return $this->codCausale;
    }

    public function setCodCausale($codCausale){
        $this->codCausale = $codCausale;
    }

    public function getDesCausale(){
        return $this->desCausale;
    }

    public function setDesCausale($desCausale){
        $this->desCausale = $desCausale;
    }

    public function getDatInserimento(){
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento){
        $this->datInserimento = $datInserimento;
    }

    public function getCatCausale(){
        return $this->catCausale;
    }

    public function setCatCausale($catCausale){
        $this->catCausale = $catCausale;
    }

    public function getCausali(){
        return $this->causali;
    }

    public function setCausali($causali){
        $this->causali = $causali;
    }

    public function getQtaCausali(){
        return $this->qtaCausali;
    }

    public function setQtaCausali($qtaCausali){
        $this->qtaCausali = $qtaCausali;
    }


    public function getQtaRegistrazioniCausale(){
        return $this->qtaRegistrazioniCausale;
    }

    public function setQtaRegistrazioniCausale($qtaRegistrazioniCausale){
        $this->qtaRegistrazioniCausale = $qtaRegistrazioniCausale;
    }

    public function getQtaContiCausale(){
        return $this->qtaContiCausale;
    }

    public function setQtaContiCausale($qtaContiCausale){
        $this->qtaContiCausale = $qtaContiCausale;
    }


    public function getContiCausale(){
        return $this->contiCausale;
    }

    public function setContiCausale($contiCausale){
        $this->contiCausale = $contiCausale;
    }

}

?>