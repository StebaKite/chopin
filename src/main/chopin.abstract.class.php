<?php

abstract class ChopinAbstract {

	public static $root;
	public static $testata;
	public static $piede;
	public static $messaggioInfo;
	public static $messaggioErrore;
	public static $azione;
	public static $testoAzione;
	public static $titoloPagina;
	public static $confermaTip;

	public static $replace;
	public static $elenco_causali;
	public static $elenco_fornitori;
	public static $elenco_clienti;
	public static $elenco_conti;
	
	public static $errorStyle = "border-color:#ff0000; border-width:1px;";
	
	// Query ------------------------------------------------------------------------------

	public static $queryRicercaCausali = "/primanota/ricercaCausali.sql";
	public static $queryRicercaFornitori = "/primanota/ricercaFornitori.sql";
	public static $queryRicercaClienti = "/primanota/ricercaClienti.sql";
	public static $queryRicercaConti = "/primanota/ricercaConti.sql";
	public static $queryLeggiIdFornitore = "/anagrafica/leggiIdFornitore.sql";
	public static $queryCreaEvento = "/main/creaEvento.sql";
	public static $queryChiudiEvento = "/main/chiudiEvento.sql";
	
	// Costruttore ------------------------------------------------------------------------
	
	function __construct() {
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new ChopinAbstract();
	
		return self::$_instance;
	}
	
	// Setters -----------------------------------------------------------------------------
	
	public function setTestata($testata) {
		self::$testata = $testata;
	}
	public function setPiede($piede) {
		self::$piede = $piede;
	}
	public function setAzione($azione) {
		self::$azione = $azione;
	}
	public function setTestoAzione($testoAzione) {
		self::$testoAzione = $testoAzione;
	}	
	public function setTitoloPagina($titoloPagina) {
		self::$titoloPagina = $titoloPagina;
	}
	public function setConfermaTip($tip) {
		self::$confermaTip = $tip;
	}
	
	// Getters -----------------------------------------------------------------------------

	public function getTestata() {
		return self::$testata;
	}
	public function getPiede() {
		return self::$piede;
	}
	public function getAzione() {
		return self::$azione;
	}
	public function getTestoAzione() {
		return self::$testoAzione;
	}
	public function getTitoloPagina() {
		return self::$titoloPagina;
	}
	public function getConfermaTip() {
		return self::$confermaTip;
	}
	
	// Start e Go funzione ----------------------------------------------------------------

	public function start() { }
			
	public function go() { }

	// Metodi per aggiornamenti e creazioni su DB  ----------------------------------------
	

	
	
	
	
	
	
	
	
	// Altri metodi di utilità ------------------------------------------------------------
	
	/**
	 * 
	 * @param $data
	 * @param $carattereSeparatore
	 * @param $gioniDaSommare
	 * @return una data in formatto d-m-Y aumentata di N giorni
	 */
	public function sommaGiorniData($data, $carattereSeparatore, $giorniDaSommare) {
		
		list($giorno, $mese, $anno) = explode($carattereSeparatore, $data);		
		return date("d/m/Y",mktime(0,0,0, $mese, $giorno + $giorniDaSommare, $anno));
	}

	/**
	 * 
	 * @param unknown $data
	 * @param unknown $carattereSeparatore
	 * @param unknown $giorniDaSommare
	 * @return string
	 */
	public function sommaGiorniDataYMD($data, $carattereSeparatore, $giorniDaSommare) {
	
		list($anno, $mese, $giorno) = explode($carattereSeparatore, $data);
		return date("Y/m/d",mktime(0,0,0, $mese, $giorno + $giorniDaSommare, $anno));
	}
	
	/**
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaCausali($utility, $db) {

		$array = $utility->getConfig();
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaCausali;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);
		
		while ($row = pg_fetch_row($result)) {
			if ($row[0] == $_SESSION["causale"]) {
				self::$elenco_causali = self::$elenco_causali . "<option value='" . $row[0] . "' selected >" . $row[0] . " - " . $row[1] . "</option>";
			}
			else {
				self::$elenco_causali = self::$elenco_causali . "<option value='" . $row[0] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			}
		}		
		return self::$elenco_causali;
	}

	/**
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaFornitori($utility, $db) {

		$array = $utility->getConfig();
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaFornitori;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);
		
		while ($row = pg_fetch_row($result)) {
			if ($row[0] == $_SESSION["fornitore"]) {
				self::$elenco_fornitori = self::$elenco_fornitori . "<option value='" . $row[0] . "' selected >" . $row[1] . " - " . $row[2] . "</option>";
			}
			else {
				self::$elenco_fornitori = self::$elenco_fornitori . "<option value='" . $row[0] . "' >" . $row[1] . " - " . $row[2] . "</option>";
			}
		}
		return self::$elenco_fornitori;		
	}

	/**
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaClienti($utility, $db) {
	
		$array = $utility->getConfig();
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaClienti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);
	
		while ($row = pg_fetch_row($result)) {
			if ($row[0] == $_SESSION["cliente"]) {
				self::$elenco_clienti = self::$elenco_clienti . "<option value='" . $row[0] . "' selected >" . $row[1] . " - " . $row[2] . "</option>";
			}
			else {
				self::$elenco_clienti = self::$elenco_clienti . "<option value='" . $row[0] . "'>" . $row[1] . " - " . $row[2] . "</option>";
			}
		}
		return self::$elenco_clienti;
	}
	
	/**
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaConti($utility, $db) {
	
		$array = $utility->getConfig();
		self::$replace = array(
				'%cod_causale%' => trim($_SESSION["causale"])
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaConti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);
	
		while ($row = pg_fetch_row($result)) {
			self::$elenco_conti = self::$elenco_conti . "<option value='" . $row[0] . $row[1] . " - " . $row[2] . "'>" . $row[2] ;
		}
		return self::$elenco_conti;
	}

	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @return unknown
	 */
	public function leggiIdFornitore($db, $utility, $idfornitore) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiIdFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @return unknown
	 */
	public function prelevaIdFornitore($db, $utility, $idfornitore) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiIdFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
	
	public function inserisciEvento($db, $utility, $datevento, $notaevento) {
		
		$array = $utility->getConfig();
		$replace = array(
				'%dat_evento%' => trim($datevento),
				'%nota_evento%' => str_replace("'", "''", trim($notaevento))
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaEvento;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;		
	}

	public function chiudiEvento($db, $utility, $idevento, $staevento) {
		
		$array = $utility->getConfig();
		$replace = array(
				'%id_evento%' => trim($idevento),
				'%sta_evento%' => trim($staevento),
				'%dat_cambio_stato%' => date("d/m/Y")
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryChiudiEvento;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
}

?>
