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
	const TIPO_RIGA_REGISTRAZIONE = "tipo";
	const RIGA_REGISTRAZIONE = "R";
	const RIGA_DETTAGLIO_REGISTRAZIONE = "D";

	// dati registrazione

	private $idRegistrazione;
	private $datScadenza;
	private $desRegistrazione;
	private $idFornitore;
	private $idCliente;
	private $codCausale;
	private $numFattura;
	private $numFatturePagate;
	private $numFattureDaPagare;
	private $datRegistrazione;
	private $datInserimento;
	private $staRegistrazione;
	private $codNegozio;
	private $idMercato;
	private $registrazioni;
	private $qtaRegistrazioni;
	private $desCliente;
	private $desFornitore;

	// Dati filtri di ricerca

	private $datRegistrazioneDa;
	private $datRegistrazioneA;
	private $codNegozioSel;
	private $codCausaleSel;

	// Queries

	const LEGGI_REGISTRAZIONE = "/primanota/leggiRegistrazione.sql";
	const CANCELLA_REGISTRAZIONE = "/primanota/deleteRegistrazione.sql";
	const RICERCA_REGISTRAZIONE = "/primanota/ricercaRegistrazione.sql";
	const CREA_REGISTRAZIONE = "/primanota/creaRegistrazione.sql";
	const AGGIORNA_REGISTRAZIONE = "/primanota/updateRegistrazione.sql";
	const CERCA_FATTURA_FORNITORE = "/primanota/ricercaFatturaFornitore.sql";
	const CERCA_FATTURA_CLIENTE = "/primanota/ricercaFatturaCliente.sql";

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
				$this->setDatScadenza(trim($row[self::DAT_SCADENZA]));
				$this->setDesRegistrazione(trim($row[self::DES_REGISTRAZIONE]));
				$this->setIdFornitore($row[self::ID_FORNITORE]);
				$this->setIdCliente($row[self::ID_CLIENTE]);
				$this->setCodCausale($row[self::COD_CAUSALE]);
				$this->setNumFattura($row[self::NUM_FATTURA]);
				$this->setDatRegistrazione(trim($row[self::DAT_REGISTRAZIONE]));
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

	public function preparaFiltri()
	{
		if ($this->getDatRegistrazioneDa() == "") $this->setDatRegistrazioneDa(date("d/m/Y"));
		if ($this->getDatRegistrazioneA() == "") $this->setDatRegistrazioneA(date("d/m/Y"));
		if ($this->getCodNegozioSel() == "") $this->setCodNegozioSel("VIL");
	}

	public function load($db)
	{
		$utility = Utility::getInstance();

		$filtriRegistrazione = "";
		$filtriDettaglio = "";

		if ($this->getCodCausaleSel() != "") {
			$filtriRegistrazione .= "and reg.cod_causale = '" . trim($this->getCodCausaleSel()) . "'";
		}
		if ($this->getCodNegozioSel() != "") {
			$filtriRegistrazione .= "and reg.cod_negozio = '" . trim($this->getCodNegozioSel()) . "'";
		}

		$replace = array(
				'%datareg_da%' => $this->getDatRegistrazioneDa(),
				'%datareg_a%' => $this->getDatRegistrazioneA(),
				'%filtri-registrazione%' => $filtriRegistrazione,
				'%filtri-dettaglio%' => $filtriDettaglio
		);

		$array = $utility->getConfig();
		$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_REGISTRAZIONE;

		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

		// esegue la query

		$result = $db->getData($sql);

		if ($result) {
			$this->setRegistrazioni(pg_fetch_all($result));

			$numReg = 0;
			foreach ($this->getRegistrazioni() as $unaRegistrazione) {
				if ($unaRegistrazione[self::TIPO_RIGA_REGISTRAZIONE] == "R") $numReg ++;
			}
			$this->setQtaRegistrazioni($numReg);
		} else {
			$this->setRegistrazioni(null);
			$this->setQtaRegistrazioni(0);
		}
		return $result;
	}

	public function inserisci($db)
	{
		$utility = Utility::getInstance();
		$cliente = Cliente::getInstance();
		$fornitore = Fornitore::getInstance();

		$cliente->setDesCliente($this->getDesCliente());
		$fornitore->setDesFornitore($this->getDesFornitore());

		if ($this->getDesCliente() != "") {
			$cliente->cercaConDescrizione($db);
			$this->setIdCliente($cliente->getIdCliente());
		}
		if ($this->getDesFornitore() != "") {
			$fornitore->cercaConDescrizione($db);
			$this->setIdFornitore($fornitore->getIdFornitore());
		}

		$array = $utility->getConfig();

		$replace = array(
				'%des_registrazione%' => str_replace("'", "''", $this->getDesRegistrazione()),
				'%dat_scadenza%' => ($this->getDatScadenza() != "") ? "'" . $this->getDatScadenza() . "'" : "null",
				'%dat_registrazione%' => ($this->getDatRegistrazione() != "") ? "'" . $this->getDatRegistrazione() . "'" : "null" ,
				'%dat_inserimento%' => date("Y-m-d H:i:s"),
				'%num_fattura%' => ($this->getNumFattura() != "") ? "'" . $this->getNumFattura() . "'" : "null" ,
				'%cod_causale%' => $this->getCodCausale(),
				'%id_fornitore%' => ($fornitore->getIdFornitore() != "") ? "'" . $fornitore->getIdFornitore() . "'" : "null" ,
				'%id_cliente%' => ($cliente->getIdCliente() != "") ? "'" . $cliente->getIdCliente() . "'" : "null" ,
				'%sta_registrazione%' => $this->getStaRegistrazione(),
				'%cod_negozio%' => ($this->getCodNegozio() != "") ? "'" . $this->getCodNegozio() . "'" : "null",
				'%id_mercato%' => ($this->getIdMercato() != "") ? "'" . $this->getIdMercato() . "'" : "null"
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$this->setIdRegistrazione($db->getLastIdUsed());		// l'id generato dall'inserimento
		}
		$_SESSION[self::REGISTRAZIONE] = serialize($this);
		$_SESSION[self::CLIENTE] = serialize($cliente);
		$_SESSION[self::FORNITORE] = serialize($fornitore);
		return $result;
	}

	public function aggiorna($db)
	{
		$utility = Utility::getInstance();
		$cliente = Cliente::getInstance();
		$fornitore = Fornitore::getInstance();

		$cliente->setDesCliente($this->getDesCliente());
		$fornitore->setDesFornitore($this->getDesFornitore());

		if ($this->getDesCliente() != "") {
			$cliente->cercaConDescrizione($db);
			$this->setIdCliente($cliente->getIdCliente());
		}
		if ($this->getDesFornitore() != "") {
			$fornitore->cercaConDescrizione($db);
			$this->setIdFornitore($fornitore->getIdFornitore());
		}

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($this->getIdRegistrazione()),
				'%des_registrazione%' => str_replace("'", "''", $this->getDesRegistrazione()),
				'%dat_scadenza%' => ($this->getDatScadenza() != "") ? "'" . $this->getDatScadenza() . "'" : "null",
				'%dat_registrazione%' => ($this->getDatRegistrazione() != "") ? "'" . $this->getDatRegistrazione() . "'" : "null" ,
				'%dat_inserimento%' => date("Y-m-d H:i:s"),
				'%num_fattura%' => ($this->getNumFattura() != "") ? "'" . $this->getNumFattura() . "'" : "null" ,
				'%cod_causale%' => $this->getCodCausale(),
				'%id_fornitore%' => ($fornitore->getIdFornitore() != "") ? "'" . $fornitore->getIdFornitore() . "'" : "null" ,
				'%id_cliente%' => ($cliente->getIdCliente() != "") ? "'" . $cliente->getIdCliente() . "'" : "null" ,
				'%sta_registrazione%' => $this->getStaRegistrazione(),
				'%cod_negozio%' => ($this->getCodNegozio() != "") ? "'" . $this->getCodNegozio() . "'" : "null",
				'%id_mercato%' => ($this->getIdMercato() != "") ? "'" . $this->getIdMercato() . "'" : "null"
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		$_SESSION[self::REGISTRAZIONE] = serialize($this);
		$_SESSION[self::CLIENTE] = serialize($cliente);
		$_SESSION[self::FORNITORE] = serialize($fornitore);
		return $result;
	}

	public function cercaFatturaFornitore($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_fornitore%' => trim($this->getIdFornitore()),
				'%num_fattura%' => trim($this->getNumFattura()),
				'%dat_registrazione%' => trim($this->getDatRegistrazione())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_FATTURA_FORNITORE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result){
			if (pg_num_rows($result) > 0) return true;
			else return false;
		}
		return false;
	}

	public function cercaFatturaCliente($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_cliente%' => trim($this->getIdCliente()),
				'%num_fattura%' => trim($this->getNumFattura()),
				'%dat_registrazione%' => trim($this->getDatRegistrazione())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_FATTURA_CLIENTE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result){
			if (pg_num_rows($result) > 0) return true;
			else return false;
		}
		return false;
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

    public function getDatRegistrazioneDa(){
        return $this->datRegistrazioneDa;
    }

    public function setDatRegistrazioneDa($datRegistrazioneDa){
        $this->datRegistrazioneDa = $datRegistrazioneDa;
    }

    public function getDatRegistrazioneA(){
        return $this->datRegistrazioneA;
    }

    public function setDatRegistrazioneA($datRegistrazioneA){
        $this->datRegistrazioneA = $datRegistrazioneA;
    }

    public function getCodNegozioSel(){
        return $this->codNegozioSel;
    }

    public function setCodNegozioSel($codNegozioSel){
        $this->codNegozioSel = $codNegozioSel;
    }

    public function getCodCausaleSel(){
        return $this->codCausaleSel;
    }

    public function setCodCausaleSel($codCausaleSel){
        $this->codCausaleSel = $codCausaleSel;
    }


    public function getRegistrazioni(){
        return $this->registrazioni;
    }

    public function setRegistrazioni($registrazioni){
        $this->registrazioni = $registrazioni;
    }

    public function getQtaRegistrazioni(){
        return $this->qtaRegistrazioni;
    }

    public function setQtaRegistrazioni($qtaRegistrazioni){
        $this->qtaRegistrazioni = $qtaRegistrazioni;
    }


    public function getDesCliente(){
        return $this->desCliente;
    }

    public function setDesCliente($desCliente){
        $this->desCliente = $desCliente;
    }

    public function getDesFornitore(){
        return $this->desFornitore;
    }

    public function setDesFornitore($desFornitore){
        $this->desFornitore = $desFornitore;
    }


    public function getNumFatturePagate(){
        return $this->numFatturePagate;
    }

    public function setNumFatturePagate($numFatturePagate){
        $this->numFatturePagate = $numFatturePagate;
        return $this;
    }

    public function getNumFattureDaPagare(){
        return $this->numFattureDaPagare;
    }

    public function setNumFattureDaPagare($numFattureDaPagare){
        $this->numFattureDaPagare = $numFattureDaPagare;
        return $this;
    }

}

?>