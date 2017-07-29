<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class DettaglioRegistrazione implements CoreInterface {

	private $root;

	// Nomi colonne tabella Registrazione

	const ID_DETTAGLIO_REGISTRAZIONE	= "id_dettaglio_registrazione";
	const ID_REGISTRAZIONE				= "id_registrazione";
	const IMP_REGISTRAZIONE				= "imp_registrazione";
	const IND_DAREAVERE					= "ind_dareavere";
	const COD_CONTO						= "cod_conto";
	const COD_SOTTOCONTO				= "cod_sottoconto";
	const DAT_INSERIMENTO				= "dat_inserimento";

	// dati registrazione

	private $idDettaglioRegistrazione;
	private $idRegistrazione;
	private $impRegistrazione;
	private $indDareavere;
	private $codConto;
	private $codSottoconto;
	private $datInserimento;

	private $dettagliRegistrazione;
	private $qtaDettagliRegistrazione;
	private $totDare;
	private $totAvere;

	// Queries

	const CREA_DETTAGLIO_REGISTRAZIONE = "/primanota/creaDettaglioRegistrazione.sql";
	const AGGIORNA_IMPORTO_DETTAGLIO_REGISTRAZIONE = "/primanota/aggiornaImportoDettaglioRegistrazione.sql";
	const AGGIORNA_SEGNO_DETTAGLIO_REGISTRAZIONE = "/primanota/aggiornaSegnoDettaglioRegistrazione.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::DETTAGLIO_REGISTRAZIONE])) $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize(new DettaglioRegistrazione());
		return unserialize($_SESSION[self::DETTAGLIO_REGISTRAZIONE]);
	}

	public function prepara()
	{
		$this->setDettagliRegistrazione(null);
		$this->setQtaDettagliRegistrazione(0);
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
	}

	public function aggiungi()
	{
		$item = array(
				DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE => trim($this->getIdDettaglioRegistrazione()),
				DettaglioRegistrazione::ID_REGISTRAZIONE => $this->getIdRegistrazione(),
				DettaglioRegistrazione::IMP_REGISTRAZIONE => trim($this->getImpRegistrazione()),
				DettaglioRegistrazione::IND_DAREAVERE => trim($this->getIndDareavere()),
				DettaglioRegistrazione::COD_CONTO => trim($this->getCodConto()),
				DettaglioRegistrazione::COD_SOTTOCONTO => trim($this->getCodSottoconto()),
				DettaglioRegistrazione::DAT_INSERIMENTO => date("Y/m/d")
		);

		if ($this->getQtaDettagliRegistrazione() == 0) {
			$resultset = array();
			array_push($resultset, $item);
			$this->setDettagliRegistrazione($resultset);
		}
		else {
			array_push($this->dettagliRegistrazione, $item);
			sort($this->dettagliRegistrazione);
		}
		$this->setQtaDettagliRegistrazione($this->getQtaDettagliRegistrazione() + 1);
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
	}

	public function cancella()
	{
		$dettagliDiff = array();
		foreach ($this->getDettagliRegistrazione() as $unDettaglio) {
			$conto = explode(" - ", trim($unDettaglio[self::COD_CONTO]));
			if ($conto[0] != trim($this->getCodConto())) {
				array_push($dettagliDiff, $unDettaglio);
			}
			else $this->setQtaDettagliRegistrazione($this->getQtaDettagliRegistrazione() - 1);
		}
		$this->setDettagliRegistrazione($dettagliDiff);
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
	}

	public function inserisci($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_registrazione%' => trim($this->getIdRegistrazione()),
				'%imp_registrazione%' => trim($this->getImpRegistrazione()),
				'%ind_dareavere%' => trim($this->getIndDareavere()),
				'%cod_conto%' => trim($this->getCodConto()),
				'%cod_sottoconto%' => trim($this->getCodSottoconto())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_DETTAGLIO_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function verificaQuadratura()
	{
		if ($this->getQtaDettagliRegistrazione() > 0)
		{
			$totale = 0;
			$totDare = 0;
			$totAvere = 0;
			foreach ($this->getDettagliRegistrazione() as $unDettaglio) {
				$importo = trim($unDettaglio[self::IMP_REGISTRAZIONE]);
				$indDareAvere = trim($unDettaglio[self::IND_DAREAVERE]);
				if ($indDareAvere == "D") $totDare += $importo;
				if ($indDareAvere == "A") $totAvere += $importo;
			}
			$totale = round($totDare, 2) - round($totAvere, 2);

			if ($totale == 0) return true;
			else return false;
		}
		else return false;
	}

	public function aggiornaImporto($db)
	{
		/**
		 * Aggiorno l'importo sulla tabella DB
		 */
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%imp_registrazione%' => $this->getImpRegistrazione(),
				'%id_dettaglio_registrazione%' => trim($this->getIdDettaglioRegistrazione()),
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_IMPORTO_DETTAGLIO_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$dettagliDiff = array();
			foreach ($this->getDettagliRegistrazione() as $unDettaglio) {

				$contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
				$codConto = explode(".", $contoComposto[0]);

				if (($codConto[0] != trim($this->getCodConto())) or	($codConto[1] != trim($this->getCodSottoconto())) )
					array_push($dettagliDiff, $unDettaglio);
				else {
					$item = array(
							DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE],
							DettaglioRegistrazione::ID_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::ID_REGISTRAZIONE],
							DettaglioRegistrazione::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
							DettaglioRegistrazione::IND_DAREAVERE => $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE],
							DettaglioRegistrazione::COD_CONTO => $unDettaglio[DettaglioRegistrazione::COD_CONTO],
							DettaglioRegistrazione::COD_SOTTOCONTO => $unDettaglio[DettaglioRegistrazione::COD_SOTTOCONTO],
							DettaglioRegistrazione::DAT_INSERIMENTO => $unDettaglio[DettaglioRegistrazione::DAT_INSERIMENTO]
					);
					array_push($dettagliDiff, $item);
				}
			}
			$this->setDettagliRegistrazione($dettagliDiff);
			$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
		}
		return $result;
	}

	public function aggiornaSegno($db)
	{
		/**
		 * Aggiorno l'importo sulla tabella DB
		 */
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%ind_dareavere%' => $this->getIndDareavere(),
				'%id_dettaglio_registrazione%' => trim($this->getIdDettaglioRegistrazione()),
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_SEGNO_DETTAGLIO_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$dettagliDiff = array();
			foreach ($this->getDettagliRegistrazione() as $unDettaglio) {

				$contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
				$codConto = explode(".", $contoComposto[0]);

				if (($codConto[0] != trim($this->getCodConto())) or	($codConto[1] != trim($this->getCodSottoconto())) )
					array_push($dettagliDiff, $unDettaglio);
					else {
						$item = array(
								DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE],
								DettaglioRegistrazione::ID_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::ID_REGISTRAZIONE],
								DettaglioRegistrazione::IMP_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE],
								DettaglioRegistrazione::IND_DAREAVERE => $this->getIndDareavere(),
								DettaglioRegistrazione::COD_CONTO => $unDettaglio[DettaglioRegistrazione::COD_CONTO],
								DettaglioRegistrazione::COD_SOTTOCONTO => $unDettaglio[DettaglioRegistrazione::COD_SOTTOCONTO],
								DettaglioRegistrazione::DAT_INSERIMENTO => $unDettaglio[DettaglioRegistrazione::DAT_INSERIMENTO]
						);
						array_push($dettagliDiff, $item);
					}
			}
			$this->setDettagliRegistrazione($dettagliDiff);
			$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
		}
		return $result;
	}

    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getIdDettaglioRegistrazione(){
        return $this->idDettaglioRegistrazione;
    }

    public function setIdDettaglioRegistrazione($idDettaglioRegistrazione){
        $this->idDettaglioRegistrazione = $idDettaglioRegistrazione;
    }

    public function getIdRegistrazione(){
        return $this->idRegistrazione;
    }

    public function setIdRegistrazione($idRegistrazione){
        $this->idRegistrazione = $idRegistrazione;
    }

    public function getImpRegistrazione(){
        return $this->impRegistrazione;
    }

    public function setImpRegistrazione($impRegistrazione){
        $this->impRegistrazione = $impRegistrazione;
    }

    public function getIndDareavere(){
        return $this->indDareavere;
    }

    public function setIndDareavere($indDareavere){
        $this->indDareavere = $indDareavere;
    }

    public function getCodConto(){
        return $this->codConto;
    }

    public function setCodConto($codConto){
        $this->codConto = $codConto;
    }

    public function getCodSottoconto(){
        return $this->codSottoconto;
    }

    public function setCodSottoconto($codSottoconto){
        $this->codSottoconto = $codSottoconto;
    }

    public function getDatInserimento(){
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento){
        $this->datInserimento = $datInserimento;
    }

    public function getDettagliRegistrazione(){
        return $this->dettagliRegistrazione;
    }

    public function setDettagliRegistrazione($dettagliRegistrazione){
        $this->dettagliRegistrazione = $dettagliRegistrazione;
    }

    public function getQtaDettagliRegistrazione(){
        return $this->qtaDettagliRegistrazione;
    }

    public function setQtaDettagliRegistrazione($qtaDettagliRegistrazione){
        $this->qtaDettagliRegistrazione = $qtaDettagliRegistrazione;
    }

    public function getTotDare(){
        return $this->totDare;
    }

    public function setTotDare($totDare){
        $this->totDare = $totDare;
    }

    public function getTotAvere(){
        return $this->totAvere;
    }

    public function setTotAvere($totAvere){
        $this->totAvere = $totAvere;
    }

}

?>