<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class ScadenzaCliente implements CoreInterface {

	private $root;

	// Nomi colonne tabella ScadenzaCliente

	const ID_SCADENZA = 'id_scadenza';
	const ID_REGISTRAZIONE = 'id_registrazione';
	const DAT_REGISTRAZIONE = 'dat_registrazione';
	const IMP_REGISTRAZIONE = 'imp_registrazione';
	const NOTA = 'nota';
	const TIP_ADDEBITO = 'tip_addebito';
	const COD_NEGOZIO = 'cod_negozio';
	const ID_CLIENTE = 'id_cliente';
	const NUM_FATTURA = 'num_fattura';
	const STA_SCADENZA = 'sta_scadenza';
	const ID_INCASSO = 'id_incasso';

	// altri nomi generati

	const DAT_SCADENZA_YYYYMMDD = "dat_scadenza_yyyymmdd";

	// dati scadenzaCliente

	private $idScadenza;
	private $idRegistrazione;
	private $datRegistrazione;
	private $impRegistrazione;
	private $nota;
	private $tipAddebito;
	private $codNegozio;
	private $idCliente;
	private $numFattura;
	private $staScadenza;
	private $idIncasso;

	private $scadenze;
	private $qtaScadenze;
	private $importoScadenza;
	private $idClienteOrig;
	private $numFatturaOrig;

	private $scadenzeDaIncassare;
	private $qtaScadenzeDaIncassare;

	// fitri di ricerca

	private $datScadenzaDa;
	private $datScadenzaA;
	private $codNegozioSel;
	private $staScadenzaSel;

	// Queries

	const CERCA_SCADENZE_CLIENTE = "/scadenze/ricercaScadenzeCliente.sql";
	const CERCA_SCADENZE_REGISTRAZIONE = "/scadenze/ricercaScadenzeClienteRegistrazione.sql";
	const CREA_SCADENZA = "/scadenze/creaScadenzaCliente.sql";
	const CANCELLA_SCADENZA = "/scadenze/cancellaScadenzaCliente.sql";
	const AGGIORNA_IMPORTO_SCADENZA_CLIENTE = "/scadenze/aggiornaImportoScadenzaCliente.sql";
	const RICERCA_SCADENZE_DA_INCASSARE = "/scadenze/ricercaScadenzeAperteCliente.sql";
	const CAMBIO_STATO_SCADENZA_CLIENTE = "/scadenze/updateStatoScadenzaCliente.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::SCADENZA_CLIENTE])) $_SESSION[self::SCADENZA_CLIENTE] = serialize(new ScadenzaCliente());
		return unserialize($_SESSION[self::SCADENZA_CLIENTE]);
	}

	public function prepara()
	{
		$this->setDatScadenzaDa(date("d/m/Y"));
		$this->setDatScadenzaA(date("d/m/Y"));
		$this->setCodNegozioSel("VIL");
		$this->setQtaScadenze(0);
		$this->setScadenze("");
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
	}

	public function load($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$filtro = "";

		if (($this->getDatScadenzaDa() != "") & ($this->getDatScadenzaA() != "")) {
			$filtro = "AND scadenza_cliente.dat_registrazione between '" . $this->getDatScadenzaDa() . "' and '" . $this->getDatScadenzaA() . "'" ;
		}

		if ($this->getCodNegozioSel() != "") {
			$filtro .= " AND scadenza_cliente.cod_negozio = '" . $this->getCodNegozioSel() . "'" ;
		}

		if ($this->getStaScadenzaSel() != "") {
			$filtro .= " AND scadenza_cliente.sta_scadenza = '" . $this->getStaScadenzaSel() . "'" ;
		}

		$replace = array(
				'%filtro_date%' => $filtro
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_CLIENTE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setScadenze(pg_fetch_all($result));
			$this->setQtaScadenze(pg_num_rows($result));
//			$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
		} else {
			$this->setScadenze(null);
			$this->setQtaScadenze(null);
//			unset($_SESSION['bottoneEstraiPdf']);
		}
		return $result;
	}

	public function trovaScadenzeRegistrazione($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($this->getIdRegistrazione())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_REGISTRAZIONE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setScadenzeDaIncassare(pg_fetch_all($result));
			$this->setQtaScadenzeDaIncassare(pg_num_rows($result));
		} else {
			$this->setScadenzeDaIncassare(null);
			$this->setQtaScadenzeDaIncassare(0);
		}
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
		return $result;
	}

	public function aggiungi()
	{
		$item = array(
				ScadenzaCliente::ID_CLIENTE => $this->getIdCliente(),
				ScadenzaCliente::DAT_REGISTRAZIONE => $this->getDatRegistrazione(),
				ScadenzaCliente::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
				ScadenzaCliente::NUM_FATTURA => $this->getNumFattura()
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
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
	}

	public function cambiaStato($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%id_incasso%' => trim($this->getIdIncasso()),
				'%sta_scadenza%' => trim($this->getStaScadenza()),
				'%id_cliente%' => trim($this->getIdCliente()),
				'%num_fattura%' => trim($this->getNumFattura())
		);

		$sqlTemplate = $this->getRoot() . $array['query'] . self::CAMBIO_STATO_SCADENZA_CLIENTE;
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
				'%dat_registrazione%' => trim($this->getDatRegistrazione()),
				'%imp_registrazione%' => trim($this->getImpRegistrazione()),
				'%nota%' => trim($this->getNota()),
				'%tip_addebito%' => trim($this->getTipAddebito()),
				'%cod_negozio%' => trim($this->getCodNegozio()),
				'%id_cliente%' => trim($this->getIdCliente()),
				'%num_fattura%' => trim($this->getNumFattura()),
				'%sta_scadenza%' => trim($this->getStaScadenza())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SCADENZA;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}


	public function cancella($db)
	{
		/**
		 * Cancello la scadenza dalla tabella DB
		 */
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$dataReg = date("d/m/Y", trim($this->getDatRegistrazione()));

		$replace = array(
				'%dat_registrazione%' => $dataReg,
				'%id_cliente%' => trim($this->getIdCliente()),
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
			foreach ($this->getScadenze() as $unaScadenza) {
				if ( ($unaScadenza[ScadenzaCliente::ID_CLIENTE] != trim($this->getIdCliente()))
				or   ($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] != $dataReg)
				or   ($unaScadenza[ScadenzaCliente::NUM_FATTURA]  != trim($this->getNumFattura())) )
				{
					array_push($scadenzeDiff, $unaScadenza);
				}
				else $this->setQtaScadenze($this->getQtaScadenze() - 1);
			}
			$this->setScadenze($scadenzeDiff);
			$_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
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
				'%imp_in_scadenza%' => $this->getImpRegistrazione(),
				'%id_fornitore%' => trim($this->getIdCliente()),
				'%dat_registrazione%' => $dataScad,
				'%num_fattura%' => $this->getNumFattura()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$scadenzeDiff = array();
			foreach ($this->getScadenze() as $unaScadenza) {

				if ($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] != $dataScad)
					array_push($scadenzeDiff, $unaScadenza);
					else {
						$item = array (
								ScadenzaCliente::ID_CLIENTE => $unaScadenza[ScadenzaCliente::ID_CLIENTEù],
								ScadenzaCliente::DAT_REGISTRAZIONE => $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE],
								ScadenzaCliente::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
								ScadenzaCliente::NUM_FATTURA => $unaScadenza[ScadenzaCliente::NUM_FATTURA]
						);
						array_push($scadenzeDiff, $item);
					}
			}
			$this->setScadenze($scadenzeDiff);
			$_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
		}
		return $result;
	}

	public function trovaScadenzeDaIncassare($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($this->getIdCliente()),
				'%cod_negozio%' => trim($this->getCodNegozioSel())
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_SCADENZE_DA_INCASSARE;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		if ($result) {
			$this->setScadenzeDaIncassare(pg_fetch_all($result));
			$this->setQtaScadenzeDaIncassare(pg_num_rows($result));
		} else {
			$this->setScadenzeDaIncassare(null);
			$this->setQtaScadenzeDaIncassare(0);
		}
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
		return $result;
	}

	// Getters & Setters

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

    public function getDatRegistrazione(){
        return $this->datRegistrazione;
    }

    public function setDatRegistrazione($datRegistrazione){
        $this->datRegistrazione = $datRegistrazione;
    }

    public function getImpRegistrazione(){
        return $this->impRegistrazione;
    }

    public function setImpRegistrazione($impRegistrazione){
        $this->impRegistrazione = $impRegistrazione;
    }

    public function getNota(){
        return $this->nota;
    }

    public function setNota($nota){
        $this->nota = $nota;
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

    public function getIdCliente(){
        return $this->idCliente;
    }

    public function setIdCliente($idCliente){
        $this->idCliente = $idCliente;
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

    public function getIdIncasso(){
        return $this->idIncasso;
    }

    public function setIdIncasso($idIncasso){
        $this->idIncasso = $idIncasso;
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


    public function getImportoScadenza(){
        return $this->importoScadenza;
    }

    public function setImportoScadenza($importoScadenza){
        $this->importoScadenza = $importoScadenza;
    }


    public function getScadenzeDaIncassare(){
        return $this->scadenzeDaIncassare;
    }

    public function setScadenzeDaIncassare($scadenzeDaIncassare){
        $this->scadenzeDaIncassare = $scadenzeDaIncassare;
    }

    public function getQtaScadenzeDaIncassare(){
        return $this->qtaScadenzeDaIncassare;
    }

    public function setQtaScadenzeDaIncassare($qtaScadenzeDaIncassare){
        $this->qtaScadenzeDaIncassare = $qtaScadenzeDaIncassare;
    }


    public function getIdClienteOrig(){
        return $this->idClienteOrig;
    }

    public function setIdClienteOrig($idClienteOrig){
        $this->idClienteOrig = $idClienteOrig;
    }

    public function getNumFatturaOrig(){
        return $this->numFatturaOrig;
    }

    public function setNumFatturaOrig($numFatturaOrig){
        $this->numFatturaOrig = $numFatturaOrig;
    }

}
?>