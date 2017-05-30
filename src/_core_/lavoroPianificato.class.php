<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class LavoroPianificato implements CoreInterface {

	private $root;

	// Nomi colonne tabella Lavoro Pianificato

	const PK_LAVORO_PIANIFICATO = "pk_lavoro_pianificato";
	const DAT_LAVORO = "dat_lavoro";
	const DES_LAVORO = "des_lavoro";
	const FIL_ESECUZIONE_LAVORO = "fil_esecuzione_lavoro";
	const CLA_ESECUZIONE_LAVORO = "cla_esecuzione_lavoro";
	const STA_LAVORO = "sta_lavoro";
	const TMS_ESECUZIONE = "tms_esecuzione";

	// altre costanti

	const PRIMO_DEL_MESE = "01";
	const SALDO_GIA_CALCOLATO = "10";
	const SALDO_DA_CALCOLARE = "00";
	const SALDI_CLASS_FOLDER = "/chopin/src/saldi/";

	// dati Lavoro Pianificato

	private $pkLavoroPianificato;
	private $datLavoro;
	private $desLavoro;
	private $filEsecuzioneLavoro;
	private $claEsecuzioneLavoro;
	private $staLavoro;
	private $tmsEsecuzione;

	private $lavoriPianificati;
	private $qtaLavoriPianificati;

	private $datRegistrazione;
	private $datEsecuzioneLavoro;

	// fitri di ricerca


	// Queries

	const LOAD_LAVORI_PIANIFICATI = "/main/lavoriPianificati.sql";
	const CAMBIO_STATO = "/main/cambioStatoLavoroPianificato.sql";

	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::LAVORO_PIANIFICATO])) $_SESSION[self::LAVORO_PIANIFICATO] = serialize(new LavoroPianificato());
		return unserialize($_SESSION[self::LAVORO_PIANIFICATO]);
	}

	public function load($db) {

		/**
		 *	colonne array LavoriPianificati
		 *
		 * 	pk_lavoro_pianificato
		 *	dat_lavoro
		 *	des_lavoro
		 *	fil_esecuzione_lavoro
		 *	cla_esecuzione_lavoro
		 *	sta_lavoro
		 *	tms_esecuzione
		 *
		 */

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array();

		$sqlTemplate = $this->getRoot() . $array['query'] . LavoroPianificato::LOAD_LAVORI_PIANIFICATI;

		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		if ($result) {
			$this->setLavoriPianificati(pg_fetch_all($result));
			$this->setQtaLavoriPianificati(pg_num_rows($result));
		}
		else {
			$this->setLavoriPianificati(null);
			$this->setQtaLavoriPianificati(0);
		}
		return $result;
	}

	public function settaDaEseguire($db) {

		if ($this->getQtaLavoriPianificati() > 0) {}
		else $this->load($db);

		if ($this->getQtaLavoriPianificati() > 0) {

			foreach($this->getLavoriPianificati() as $row) {

				/**
				 * Se la registrazione ha una data di registrazione che cade all'interno di un mese per il quale è già
				 * stato riportato il saldo allora devo aggiornare tutti i riporti da quella data riporto in poi
				 *
				 * Salto tutti gli eventuali lavori pianificati che cadono in giorni diversi dal primo del mese
				 */

				if (date("d", strtotime($row[LavoroPianificato::DAT_LAVORO])) == LavoroPianificato::PRIMO_DEL_MESE) {
					if ((strtotime($row[LavoroPianificato::DAT_LAVORO]) >= strtotime($this->getDatRegistrazione())) && ($row[LavoroPianificato::STA_LAVORO] == LavoroPianificato::SALDO_GIA_CALCOLATO)) {

						$this->setPkLavoroPianificato($row[LavoroPianificato::PK_LAVORO_PIANIFICATO]);
						$this->setStaLavoro(LavoroPianificato::SALDO_DA_CALCOLARE);
						$this->cambioStato($db);
					}
				}
			}
		}
		$this->load($db);	// Riestrazione dei lavori pianificati a valle dei cambi stato
	}

	public function cambioStato($db)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$replace = array(
				'%sta_lavoro%' => $this->getStaLavoro(),
				'%pk_lavoro_pianificato%' => $this->getPkLavoroPianificato()
		);
		$sqlTemplate = $this->getRoot() . $array['query'] . LavoroPianificato::CAMBIO_STATO;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function esegui($db)
	{
		$oggi = date("Y/m/d");
		foreach($this->getLavoriPianificati() as $row) {
			if ((strtotime($row[self::DAT_LAVORO]) <= strtotime($oggi)) && ($row[self::STA_LAVORO] == self::SALDO_DA_CALCOLARE)) {

				$this->setClaEsecuzioneLavoro($row[self::CLA_ESECUZIONE_LAVORO]);
				$this->setFilEsecuzioneLavoro($row[self::FIL_ESECUZIONE_LAVORO]);
				$this->setDatLavoro($row[self::DAT_LAVORO]);
				$this->setPkLavoroPianificato($row[self::PK_LAVORO_PIANIFICATO]);

				if ($this->runClass($db)) error_log("Lavoro " . $this->getDesLavoro() . " eseguito!");
				else error_log("Lavoro " . $this->getDesLavoro() . " in crash!");
			}
		}
	}

	public function runClass($db)
	{
		$className = trim($this->getClaEsecuzioneLavoro());
		$fileClass = $this->getRoot() . self::SALDI_CLASS_FOLDER . trim($this->getFilEsecuzioneLavoro()) . '.class.php';

		if (file_exists($fileClass)) {

			require_once trim($this->getFilEsecuzioneLavoro()) . '.class.php';

			if (class_exists($className)) {
				$instance = new $className();
				$this->setDatEsecuzioneLavoro(str_replace("-", "/", $this->getDatLavoro()));
				if ($instance->start($db, $this->getLavoroPianificato())) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				error_log("Il nome classe '" . $className . "' non è definito, lavoro non eseguito");
				return false;
			}
		}
		else {
			error_log("Il file '" . $fileClass . "' non esiste, lavoro non eseguito");
			return false;
		}
	}

	// Getters e Setters

    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getPkLavoroPianificato(){
        return $this->pkLavoroPianificato;
    }

    public function setPkLavoroPianificato($pkLavoroPianificato){
        $this->pkLavoroPianificato = $pkLavoroPianificato;
    }

    public function getDatLavoro(){
        return $this->datLavoro;
    }

    public function setDatLavoro($datLavoro){
        $this->datLavoro = $datLavoro;
    }

    public function getDesLavoro(){
        return $this->desLavoro;
    }

    public function setDesLavoro($desLavoro){
        $this->desLavoro = $desLavoro;
    }

    public function getFilEsecuzioneLavoro(){
        return $this->filEsecuzioneLavoro;
    }

    public function setFilEsecuzioneLavoro($filEsecuzioneLavoro){
        $this->filEsecuzioneLavoro = $filEsecuzioneLavoro;
    }

    public function getClaEsecuzioneLavoro(){
        return $this->claEsecuzioneLavoro;
    }

    public function setClaEsecuzioneLavoro($claEsecuzioneLavoro){
        $this->claEsecuzioneLavoro = $claEsecuzioneLavoro;
    }

    public function getStaLavoro(){
        return $this->staLavoro;
    }

    public function setStaLavoro($staLavoro){
        $this->staLavoro = $staLavoro;
    }

    public function getTmsEsecuzione(){
        return $this->tmsEsecuzione;
    }

    public function setTmsEsecuzione($tmsEsecuzione){
        $this->tmsEsecuzione = $tmsEsecuzione;
    }

    public function getLavoriPianificati(){
        return $this->lavoriPianificati;
    }

    public function setLavoriPianificati($lavoriPianificati){
        $this->lavoriPianificati = $lavoriPianificati;
    }

    public function getQtaLavoriPianificati(){
        return $this->qtaLavoriPianificati;
    }

    public function setQtaLavoriPianificati($qtaLavoriPianificati){
        $this->qtaLavoriPianificati = $qtaLavoriPianificati;
    }

    public function getDatEsecuzioneLavoro(){
        return $this->datEsecuzioneLavoro;
    }

    public function setDatEsecuzioneLavoro($datEsecuzioneLavoro){
        $this->datEsecuzioneLavoro = $datEsecuzioneLavoro;
    }


    public function getDatRegistrazione(){
        return $this->datRegistrazione;
    }

    public function setDatRegistrazione($datRegistrazione){
        $this->datRegistrazione = $datRegistrazione;
    }

}

?>