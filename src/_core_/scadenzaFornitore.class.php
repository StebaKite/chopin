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

	private $scadenzeDaPagare;
	private $qtaScadenzeDaPagare;
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
	const CANCELLA_SCADENZA = "/scadenze/cancellaScadenzaFornitore.sql";
	const AGGIORNA_IMPORTO_SCADENZA_FORNITORE = "/scadenze/aggiornaImportoScadenzaFornitore.sql";
	const RICERCA_SCADENZE_DA_PAGARE = "/scadenze/ricercaScadenzeAperteFornitore.sql";

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
		$this->setQtaScadenzeDaPagare(0);
		$this->setScadenzeDaPagare("");
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
			$this->setScadenzeDaPagare(pg_fetch_all($result));
			$this->setQtaScadenzeDaPagare(pg_num_rows($result));
		} else {
			$this->setScadenzeDaPagare(null);
			$this->setQtaScadenzeDaPagare(null);
		}
		return $result;
	}

	public function cambiaStato($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_pagamento%' => trim($this->getIdPagamento()),
				'%sta_scadenza%' => trim($this->getStaScadenza()),
				'%id_fornitore%' => trim($this->getIdFornitore()),
				'%num_fattura%' => trim($this->getNumFattura()),
				'%dat_scadenza%' => trim($this->getDatScadenza())
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::CAMBIO_STATO_SCADENZA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		return $result;
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
				ScadenzaFornitore::ID_FORNITORE => $this->getIdFornitore(),
				ScadenzaFornitore::DAT_SCADENZA => $this->getDatScadenza(),
				ScadenzaFornitore::IMP_IN_SCADENZA => $this->getImpInScadenza(),
				ScadenzaFornitore::NUM_FATTURA => $this->getNumFattura()
		);

		if ($this->getQtaScadenzeDaPagare() == 0) {
			$resultset = array();
			array_push($resultset, $item);
			$this->setScadenzeDaPagare($resultset);
		}
		else {
			array_push($this->scadenzeDaPagare, $item);
			sort($this->scadenzeDaPagare);
		}
		$this->setQtaScadenzeDaPagare($this->getQtaScadenzeDaPagare() + 1);
		$_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
	}

	public function cancella($db)
	{
		/**
		 * Cancello la scadenza dalla tabella DB
		 */
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$dataScad = date("d/m/Y", trim($this->getDatScadenza()));

		$replace = array(
				'%dat_scadenza%' => $dataScad,
				'%id_fornitore%' => trim($this->getIdFornitore()),
				'%num_fattura%' => trim($this->getNumFattura())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_SCADENZA;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			/**
			 * Elimino la scadenza dalla griglia in pagina
			 */
			$scadenzeDiff = array();
			foreach ($this->getScadenzeDaPagare() as $unaScadenza) {
				if ( ($unaScadenza[ScadenzaFornitore::ID_FORNITORE] != trim($this->getIdFornitore()))
				or   ($unaScadenza[ScadenzaFornitore::DAT_SCADENZA] != $dataScad)
				or   ($unaScadenza[ScadenzaFornitore::NUM_FATTURA]  != trim($this->getNumFattura())) )
				{
					array_push($scadenzeDiff, $unaScadenza);
				}
				else $this->setQtaScadenzeDaPagare($this->getQtaScadenzeDaPagare() - 1);
			}
			$this->setScadenzeDaPagare($scadenzeDiff);
			$_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
		}
		return $result;
	}

	public function aggiornaImporto($db)
	{
		/**
		 * Aggiorno l'importo in scadenza sulla tabella DB
		 */
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$dataScad = date("d/m/Y", trim($this->getDatScadenza()));

		$replace = array(
				'%imp_in_scadenza%' => $this->getImpInScadenza(),
				'%id_fornitore%' => trim($this->getIdFornitore()),
				'%dat_scadenza%' => $dataScad,
				'%num_fattura%' => $this->getNumFattura()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$scadenzeDiff = array();
			foreach ($this->getScadenzeDaPagare() as $unaScadenza) {

				if ($unaScadenza[ScadenzaFornitore::DAT_SCADENZA] != $dataScad)
					array_push($scadenzeDiff, $unaScadenza);
				else {
					$item = array (
						ScadenzaFornitore::ID_FORNITORE => $unaScadenza[ScadenzaFornitore::ID_FORNITORE],
						ScadenzaFornitore::DAT_SCADENZA => $unaScadenza[ScadenzaFornitore::DAT_SCADENZA],
						ScadenzaFornitore::IMP_IN_SCADENZA => $this->getImpInScadenza(),
						ScadenzaFornitore::NUM_FATTURA => $unaScadenza[ScadenzaFornitore::NUM_FATTURA]
					);
					array_push($scadenzeDiff, $item);
				}
			}
			$this->setScadenzeDaPagare($scadenzeDiff);
			$_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
		}
		return $result;
	}

	public function trovaScadenzeDaPagare($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($this->getIdFornitore()),
				'%cod_negozio%' => trim($this->getCodNegozioSel())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_SCADENZE_DA_PAGARE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setScadenzeDaPagare(pg_fetch_all($result));
			$this->setQtaScadenzeDaPagare(pg_num_rows($result));
		} else {
			$this->setScadenzeDaPagare(null);
			$this->setQtaScadenzeDaPagare(0);
		}
		$_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
		return $result;
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


    public function getScadenzeDaPagare(){
        return $this->scadenzeDaPagare;
    }

    public function setScadenzeDaPagare($scadenze){
        $this->scadenzeDaPagare = $scadenze;
    }

    public function getQtaScadenzeDaPagare(){
        return $this->qtaScadenzeDaPagare;
    }

    public function setQtaScadenzeDaPagare($qtaScadenze){
        $this->qtaScadenzeDaPagare = $qtaScadenze;
    }


    public function getImportoScadenza(){
        return $this->importoScadenza;
    }

    public function setImportoScadenza($importoScadenza){
        $this->importoScadenza = $importoScadenza;
    }

}

?>