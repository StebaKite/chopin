<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class ScadenzaFornitore implements CoreInterface {

	private $root;

	// Nomi colonne tabella Scadenza

	const ID_SCADENZA = "id_scadenza";
	const ID_REGISTRAZIONE = "id_registrazione";
	const DAT_SCADENZA = "dat_scadenza";
	const IMP_IN_SCADENZA = "imp_in_scadenza";
	const NOTA_SCADENZA = "nota_scadenza";
	const TIP_ADDEBITO = "tip_addebito";
	const COD_NEGOZIO = "cod_negozio";
	const ID_FORNITORE = "id_fornitore";
	const NUM_FATTURA = "num_fattura";
	const STA_SCADENZA = "sta_scadenza";
	const ID_PAGAMENTO = "id_pagamento";

	// altri nomi generati

	const DAT_SCADENZA_YYYYMMDD = "dat_scadenza_yyyymmdd";

	// dati scadenza

	private $idScadenza;
	private $idRegistrazione;
	private $datScadenza;
	private $impInScadenza;
	private $notaScadenza;
	private $tipAddebito;
	private $codNegozio;
	private $idFornitore;
	private $numFattura;
	private $staScadenza;
	private $idPagamento;

	private $scadenze;
	private $qtaScadenze;
	private $importoScadenza;

	// fitri di ricerca

	private $datScadenzaDa;
	private $datScadenzaA;
	private $codNegozioSel;
	private $staScadenzaSel;

	// Queries

	const CERCA_SCADENZE_FORNITORE = "/scadenze/ricercaScadenzeFornitore.sql";
	const CAMBIO_STATO_SCADENZA_FORNITORE = "/scadenze/updateStatoScadenzaFornitore.sql";
	const CREA_SCADENZA = "/scadenze/creaScadenzaFornitore.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::SCADENZA_FORNITORE])) $_SESSION[self::SCADENZA_FORNITORE] = serialize(new ScadenzaFornitore());
		return unserialize($_SESSION[self::SCADENZA_FORNITORE]);
	}

	public function prepara()
	{
		$this->setDatScadenzaDa(date("d/m/Y"));
		$this->setDatScadenzaA(date("d/m/Y"));
		$this->setCodNegozioSel("VIL");
		$this->setQtaScadenze(0);
		$this->setScadenze("");
		$_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
	}

	public function load($db)
	{
		/**
		 * Colonne array scadenze
		 *
		 * 	id_scadenza,
		 *	id_registrazione,
		 *  id_fornitore,
		 *	sta_registrazione,
		 *	des_fornitore,
		 *	dat_scadenza_yyyymmdd,
		 *	dat_scadenza,
		 *	dat_scadenza_originale,
		 *	imp_in_scadenza,
		 *	nota_scadenza,
		 *	tip_addebito,
		 *	num_fattura,
		 *	sta_scadenza,
		 *	id_pagamento
		 */

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$filtro = "";

		if (($this->getDatScadenzaDa() != "") & ($this->getDatScadenzaA() != "")) {
			$filtro = "AND scadenza.dat_scadenza between '" . $this->getDatScadenzaDa() . "' and '" . $this->getDatScadenzaA() . "'" ;
		}

		if ($this->getCodNegozioSel() != "") {
			$filtro .= " AND scadenza.cod_negozio = '" . $this->getCodNegozioSel() . "'" ;
		}

		if ($this->getStaScadenzaSel() != "") {
			$filtro .= " AND scadenza.sta_scadenza = '" . $this->getStaScadenzaSel() . "'" ;
		}

		$replace = array(
				'%filtro_date%' => $filtro
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setScadenze(pg_fetch_all($result));
			$this->setQtaScadenze(pg_num_rows($result));
		} else {
			$this->setScadenze(null);
			$this->setQtaScadenze(null);
		}
		return $result;
	}

	public function cambiaStato($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_scadenza%' => trim($this->getIdScadenza()),
				'%sta_scadenza%' => trim($this->getStaScadenza())
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::CAMBIO_STATO_SCADENZA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	}

	public function inserisci($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_registrazione%' => trim($this->getIdRegistrazione()),
				'%dat_scadenza%' => trim($this->getDatScadenza()),
				'%imp_in_scadenza%' => trim($this->getImpInScadenza()),
				'%nota_in_scadenza%' => trim($this->getNotaScadenza()),
				'%tip_addebito%' => trim($this->getTipAddebito()),
				'%cod_negozio%' => trim($this->getCodNegozio()),
				'%id_fornitore%' => trim($this->getIdFornitore()),
				'%num_fattura%' => trim($this->getNumFattura()),
				'%sta_scadenza%' => trim($this->getStaScadenza())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SCADENZA;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function aggiungi()
	{
		$item = array(
				ScadenzaFornitore::DAT_SCADENZA => $this->getDatScadenza(),
				ScadenzaFornitore::IMP_IN_SCADENZA => $this->getImpInScadenza(),
		);

		if ($this->getQtaScadenze() == 0) {
			$resultset = array();
			array_push($resultset, $item);
			$this->setScadenze($resultset);
		}
		else {
			array_push($this->scadenze, $item);
			sort($this->scadenze);
		}
		$this->setQtaScadenze($this->getQtaScadenze() + 1);
		$_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
	}

	/************************************************************************
	 * Getters e setters
	 */

    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getIdScadenza(){
        return $this->idScadenza;
    }

    public function setIdScadenza($idScadenza){
        $this->idScadenza = $idScadenza;
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

    public function getImpInScadenza(){
        return $this->impInScadenza;
    }

    public function setImpInScadenza($impInScadenza){
        $this->impInScadenza = $impInScadenza;
    }

    public function getNotaScadenza(){
        return $this->notaScadenza;
    }

    public function setNotaScadenza($notaScadenza){
        $this->notaScadenza = $notaScadenza;
    }

    public function getTipAddebito(){
        return $this->tipAddebito;
    }

    public function setTipAddebito($tipAddebito){
        $this->tipAddebito = $tipAddebito;
    }

    public function getCodNegozio(){
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio){
        $this->codNegozio = $codNegozio;
    }

    public function getIdFornitore(){
        return $this->idFornitore;
    }

    public function setIdFornitore($idFornitore){
        $this->idFornitore = $idFornitore;
    }

    public function getNumFattura(){
        return $this->numFattura;
    }

    public function setNumFattura($numFattura){
        $this->numFattura = $numFattura;
    }

    public function getStaScadenza(){
        return $this->staScadenza;
    }

    public function setStaScadenza($staScadenza){
        $this->staScadenza = $staScadenza;
    }

    public function getIdPagamento(){
        return $this->idPagamento;
    }

    public function setIdPagamento($idPagamento){
        $this->idPagamento = $idPagamento;
    }


    public function getDatScadenzaDa(){
        return $this->datScadenzaDa;
    }

    public function setDatScadenzaDa($datScadenzaDa){
        $this->datScadenzaDa = $datScadenzaDa;
    }

    public function getDatScadenzaA(){
        return $this->datScadenzaA;
    }

    public function setDatScadenzaA($datScadenzaA){
        $this->datScadenzaA = $datScadenzaA;
    }


    public function getCodNegozioSel(){
        return $this->codNegozioSel;
    }

    public function setCodNegozioSel($codNegozioSel){
        $this->codNegozioSel = $codNegozioSel;
    }

    public function getStaScadenzaSel(){
        return $this->staScadenzaSel;
    }

    public function setStaScadenzaSel($staScadenzaSel){
        $this->staScadenzaSel = $staScadenzaSel;
    }


    public function getScadenze(){
        return $this->scadenze;
    }

    public function setScadenze($scadenze){
        $this->scadenze = $scadenze;
    }

    public function getQtaScadenze(){
        return $this->qtaScadenze;
    }

    public function setQtaScadenze($qtaScadenze){
        $this->qtaScadenze = $qtaScadenze;
    }


    public function getImportoScadenza(){
        return $this->importoScadenza;
    }

    public function setImportoScadenza($importoScadenza){
        $this->importoScadenza = $importoScadenza;
    }

}

?>