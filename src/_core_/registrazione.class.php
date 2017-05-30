<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Registrazione implements CoreInterface {

	private $root;

	// Nomi colonne tabella Registrazione

	const ID_REGISTRAZIONE	= "id_registrazione";
	const DAT_SCADENZA		= "dat_scadenza";
	const DES_REGISTRAZIONE	= "des_registrazione";
	const ID_FORNITORE		= "id_fornitore";
	const ID_CLIENTE		= "id_cliente";
	const COD_CAUSALE		= "cod_causale";
	const NUM_FATTURA		= "num_fattura";
	const DAT_REGISTRAZIONE	= "dat_registrazione";
	const DAT_INSERIMENTO	= "dat_inserimento";
	const STA_REGISTRAZIONE	= "sta_registrazione";
	const COD_NEGOZIO		= "cod_negozio";
	const ID_MERCATO		= "id_mercato";

	// altri nomi generati

	const DAT_REGISTRAZIONE_YYYYMMDD = "dat_registrazione_yyyymmdd";

	// dati registrazione

	private $idRegistrazione;
	private $datScadenza;
	private $desRegistrazione;
	private $idFornitore;
	private $idCliente;
	private $codCausale;
	private $numFattura;
	private $datRegistrazione;
	private $datInserimento;
	private $staRegistrazione;
	private $codNegozio;
	private $idMercato;

	// Queries

	const LEGGI_REGISTRAZIONE = "/primanota/leggiRegistrazione.sql";
	const CANCELLA_REGISTRAZIONE = "/primanota/deleteRegistrazione.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::REGISTRAZIONE])) $_SESSION[self::REGISTRAZIONE] = serialize(new Registrazione());
		return unserialize($_SESSION[self::REGISTRAZIONE]);
	}

	public function leggi($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_registrazione%' => trim($this->getIdRegistrazione())
		);

		$sqlTemplate = $this->root . $array['query'] . self::LEGGI_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			foreach (pg_fetch_all($result) as $row) {
				$this->setIdRegistrazione($row[self::ID_REGISTRAZIONE]);
				$this->setDatScadenza($row[self::DAT_SCADENZA]);
				$this->setDesRegistrazione($row[self::DES_REGISTRAZIONE]);
				$this->setIdFornitore($row[self::ID_FORNITORE]);
				$this->setIdCliente($row[self::ID_CLIENTE]);
				$this->setCodCausale($row[self::COD_CAUSALE]);
				$this->setNumFattura($row[self::NUM_FATTURA]);
				$this->setDatRegistrazione($row[self::DAT_REGISTRAZIONE]);
				$this->setDatInserimento($row[self::DAT_INSERIMENTO]);
				$this->setStaRegistrazione($row[self::STA_REGISTRAZIONE]);
				$this->setCodNegozio($row[self::COD_NEGOZIO]);
				$this->setIdMercato($row[self::ID_MERCATO]);
			}
		}
		return $result;
	}

	public function cancella($db) {

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_registrazione%' => trim($this->getIdRegistrazione())
		);

		$sqlTemplate = $this->root . $array['query'] . self::CANCELLA_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}





    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getIdRegistrazione(){
        return $this->idRegistrazione;
    }

    public function setIdRegistrazione($idRegistrazione){
        $this->idRegistrazione = $idRegistrazione;
    }

    public function getDatScadenza(){
        return $this->datScadenza;
    }

    public function setDatScadenza($datScadenza){
        $this->datScadenza = $datScadenza;
    }

    public function getDesRegistrazione(){
        return $this->desRegistrazione;
    }

    public function setDesRegistrazione($desRegistrazione){
        $this->desRegistrazione = $desRegistrazione;
    }

    public function getIdFornitore(){
        return $this->idFornitore;
    }

    public function setIdFornitore($idFornitore){
        $this->idFornitore = $idFornitore;
    }

    public function getIdCliente(){
        return $this->idCliente;
    }

    public function setIdCliente($idCliente){
        $this->idCliente = $idCliente;
    }

    public function getCodCausale(){
        return $this->codCausale;
    }

    public function setCodCausale($codCausale){
        $this->codCausale = $codCausale;
    }

    public function getNumFattura(){
        return $this->numFattura;
    }

    public function setNumFattura($numFattura){
        $this->numFattura = $numFattura;
    }

    public function getDatRegistrazione(){
        return $this->datRegistrazione;
    }

    public function setDatRegistrazione($datRegistrazione){
        $this->datRegistrazione = $datRegistrazione;
    }

    public function getDatInserimento(){
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento){
        $this->datInserimento = $datInserimento;
    }

    public function getStaRegistrazione(){
        return $this->staRegistrazione;
    }

    public function setStaRegistrazione($staRegistrazione){
        $this->staRegistrazione = $staRegistrazione;
    }

    public function getCodNegozio(){
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio){
        $this->codNegozio = $codNegozio;
    }

    public function getIdMercato(){
        return $this->idMercato;
    }

    public function setIdMercato($idMercato){
        $this->idMercato = $idMercato;
    }
}

?>