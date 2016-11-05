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
	public static $elenco_mercati;
	
	public static $errorStyle = "border-color:#ff0000; border-width:1px;";

	public static $sourceFolder = "/chopin/src/saldi/";
	
	// Query ------------------------------------------------------------------------------

	public static $queryRicercaCausali = "/primanota/ricercaCausali.sql";
	public static $queryRicercaFornitori = "/primanota/ricercaFornitori.sql";
	public static $queryRicercaClienti = "/primanota/ricercaClienti.sql";
	public static $queryRicercaConti = "/primanota/ricercaConti.sql";
	public static $queryLeggiIdFornitore = "/anagrafica/leggiIdFornitore.sql";
	public static $queryTrovaDescrizioneFornitore = "/anagrafica/trovaDescFornitore.sql";
	public static $queryTrovaDescrizioneCliente = "/anagrafica/trovaDescCliente.sql";
	public static $queryLeggiIdCliente = "/anagrafica/leggiIdCliente.sql";
	public static $queryCreaEvento = "/main/creaEvento.sql";
	public static $queryChiudiEvento = "/main/chiudiEvento.sql";
	public static $queryCreaSaldo = "/saldi/creaSaldo.sql";
	public static $queryAggiornaSaldo = "/saldi/aggiornaSaldo.sql";
	public static $queryLeggiSaldo = "/saldi/leggiSaldo.sql";
	public static $queryCancellaSaldo = "/saldi/cancellaSaldo.sql";
	public static $queryCambioStatoLavoroPianificato = "/main/cambioStatoLavoroPianificato.sql";
	public static $queryLavoriPianificati = "/main/lavoriPianificati.sql";
	public static $queryLavoriPianificatiAnnoCorrente = "/main/lavoriPianificatiAnnoCorrente.sql";

	public static $queryCreaSottoconto = "/configurazioni/creaSottoconto.sql";
	public static $queryRicercacCategorie = "/anagrafica/leggiCategorieCliente.sql";

	public static $queryLeggiTuttiConti = "/configurazioni/leggiTuttiConti.sql";

	public static $queryRicercaMercati = "/configurazioni/leggiTuttiMercati.sql";
	public static $queryRicercaMercatiNegozio = "/primanota/ricercaMercati.sql";
	
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
	
	// Altri metodi di utilità ------------------------------------------------------------

	/**
	 * Questo metodo determiona se l'anno è bisestile
	 * @param unknown $anno
	 */
	public function isAnnoBisestile($anno) {
		
		$annoBisestile = false;
		
		if (($anno%4 == 0 && $anno%100 != 0) || $anno%400 == 0) {
			$annoBisestile = true;
		}		
		return $annoBisestile;
	}
	
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
	 * Questo metodo carica una categoria di causali
	 *  
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaCausali($utility, $db, $categoria) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_causale%' => trim($categoria)
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaCausali;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
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

	// Metodi comuni per accessi sul DB  ----------------------------------------
	
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
		
		/**
		 * Prepara un elenco da inserire in una array javascript adatta per un campo autocomplete
		 */
		while ($row = pg_fetch_row($result)) {
			self::$elenco_fornitori = self::$elenco_fornitori . '"' . $row[2] . '",';
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

		/**
		 * Prepara un elenco da inserire in una array javascript adatta per un campo autocomplete
		 */
		while ($row = pg_fetch_row($result)) {
			self::$elenco_clienti = self::$elenco_clienti . '"' . $row[2] . '",';
		}
		return self::$elenco_clienti;
	}
	
	/**
	 * Questo metodo carica tutti i conti configurati su una causale
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
	 * Questo metodo carica tutti i conti esistenti nel piano dei conti
	 * @param unknown $utility
	 * @param unknown $db
	 */
	public function caricaTuttiConti($utility, $db) {

		$array = $utility->getConfig();
		self::$replace = array();
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiTuttiConti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);

		while ($row = pg_fetch_row($result)) {
			if ($row[0] . " - " . $row[1] == $_SESSION["conto_sel"]) {
				self::$elenco_conti = self::$elenco_conti . "<option value='" . $row[0] . " - " . $row[1] . "' selected>" . $row[0].$row[1] . " - " . $row[2] ;
			}
			else {
				self::$elenco_conti = self::$elenco_conti . "<option value='" . $row[0] . " - " . $row[1] . "'>" . $row[0].$row[1] . " - " . $row[2] ;
			}
		}
		return self::$elenco_conti;	
	}

	/**
	 * Questo metodo carica tutti i mercati
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaMercati($utility, $db) {
	
		$array = $utility->getConfig();
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaMercati;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);

		while ($row = pg_fetch_row($result)) {
			if ($row[0] == $_SESSION["idmercato"]) {
				self::$elenco_mercati .= "<option value='" . $row[0] . "' selected>" . $row[2] . "</option>";
			}
			else {
				self::$elenco_mercati .= "<option value='" . $row[0] . "'>" . $row[2] . "</option>";
			}		
		}		
		return self::$elenco_mercati;
	}

	public function caricaMercatiNegozio($utility, $db) {
	
		$array = $utility->getConfig();
		self::$replace = array(
				'%cod_negozio%' => trim($_SESSION["codneg"])
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaMercatiNegozio;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);
		
		while ($row = pg_fetch_row($result)) {
			if ($row[0] == $_SESSION["idmercato"]) {
				self::$elenco_mercati .= "<option value='" . $row[0] . "' selected>" . $row[1] . "</option>";
			}
			else {
				self::$elenco_mercati .= "<option value='" . $row[0] . "'>" . $row[1] . "</option>";
			}
		}
		return self::$elenco_mercati;
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
	 * @param unknown $idcliente
	 * @return unknown
	 */
	public function leggiIdCliente($db, $utility, $idcliente) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiIdCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo recupera l'id di un fornitore accedendo con la descrizione
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $desfornitore
	 * @return unknown
	 */
	public function leggiDescrizioneFornitore($db, $utility, $desfornitore) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%des_fornitore%' => trim($desfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaDescrizioneFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		
		$rows = pg_fetch_all($result);
		
		foreach($rows as $row) {
			$descrizione_fornitore = $row['id_fornitore'];
		}		
		return $descrizione_fornitore;
	}
	
	/**
	 * Questo metodo recupera l'id di un cliente accedendo con la descrizione
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $descliente
	 * @return unknown
	 */
	public function leggiDescrizioneCliente($db, $utility, $descliente) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%des_cliente%' => trim($descliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaDescrizioneCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		$rows = pg_fetch_all($result);
	
		foreach($rows as $row) {
			$descrizione_cliente = $row['id_cliente'];
		}
		return $descrizione_cliente;
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

	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $datevento
	 * @param unknown $notaevento
	 * @return unknown
	 */
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

	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idevento
	 * @param unknown $staevento
	 * @return unknown
	 */
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

	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $dataRegistrazione = strtotime(str_replace('/', '-', $data1));
	 */
	public function rigenerazioneSaldi($db, $utility, $dataRegistrazione, $project_root) {

		$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
		
		if ($lavoriPianificati) {
			$rows = pg_fetch_all($lavoriPianificati);
				
			foreach($rows as $row) {

				/**
				 * Se la registrazione ha una data di registrazione che cade all'interno di un mese per il quale è già
				 * stato riportato il saldo allora devo aggiornare tutti i riporti da quella data riporto in poi
				 * 
				 * Salto tutti gli eventuali lavori pianificati che cadono in giorni diversi dal primo del mese 
				 */				

				if (date("d", strtotime($row['dat_lavoro'])) == "01") {
					if ((strtotime($row['dat_lavoro']) >= $dataRegistrazione) && ($row['sta_lavoro'] == "10")) {
						$this->cambioStatoLavoroPianificato($db, $utility, $row['pk_lavoro_pianificato'], '00');
					}						
				}
			}
		}
		
		/**
		 * Riestrazione dei lavori pianificati a valle della verifica e aggiornamento stati ed riesecuzione dei lavori
		 * Attenzione che vengono rieseguiti tutti i lavori pianificati anche quelli che non riguardano l'aggiornamento
		 * dei saldi. E' importante che i lavori pianificabili siano rieseguibili.
		 */
		$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
		$this->eseguiLavoriPianificati($db, $lavoriPianificati, $project_root);		
	} 
	
	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $pklavoro
	 * @param unknown $stato
	 * @return unknown
	 */
	public function cambioStatoLavoroPianificato($db, $utility, $pklavoro, $stato) {
	
		$replace = array(
				'%sta_lavoro%' => $stato,
				'%pk_lavoro_pianificato%' => $pklavoro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryCambioStatoLavoroPianificato;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @return unknown
	 */
	public function leggiLavoriPianificati($db, $utility) {
	
		$replace = array();
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryLavoriPianificati;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		return $result;
	}	

	public function leggiLavoriPianificatiBatchMode($db, $utility, $project_root) {
	
		$replace = array();
	
		$array = $utility->getConfig();
		$sqlTemplate = $project_root . $array['query'] . self::$queryLavoriPianificati;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		return $result;
	}
	
	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @return unknown
	 */
	public function leggiLavoriPianificatiAnnoCorrente($db, $utility) {
	
		$replace = array();
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryLavoriPianificatiAnnoCorrente;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		return $result;
	}
	
	public function eseguiLavoriPianificati($db, $lavoriPianificati, $project_root) {
		
		$rows = pg_fetch_all($lavoriPianificati);
		$_SESSION["lavoriPianificati"] = $rows;
		
		$oggi = date("Y/m/d");
		
		foreach($rows as $row) {
				
			if ((strtotime($row['dat_lavoro']) <= strtotime($oggi)) && ($row['sta_lavoro'] == "00")) {
		
				$this->eseguiLavoro($db, $row, $project_root);
			}
		}
	}
	
	/**
	 * 
	 * @param unknown $db
	 * @param unknown $row
	 * @return boolean
	 */
	public function eseguiLavoro($db, $row, $project_root) {
	
		$className = trim($row['cla_esecuzione_lavoro']);
		$fileClass = $project_root . self::$sourceFolder . trim($row['fil_esecuzione_lavoro']) . '.class.php';
	
		if (file_exists($fileClass)) {
	
			require_once trim($row['fil_esecuzione_lavoro']) . '.class.php';
	
			if (class_exists($className)) {
				$instance = new $className();
				$_SESSION["dataEsecuzioneLavoro"] = str_replace("-", "/", $row["dat_lavoro"]);
				if ($instance->start($db, $row['pk_lavoro_pianificato'])) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				echo "Il nome classe '" . $className . "' non è definito, lavoro non eseguito";
				return FALSE;
			}
		}
		else {
			error_log("Il file '" . $fileClass . "' non esiste, lavoro non eseguito");
			return FALSE;
		}
	}	

	/**
	 * Questo metodo inserisce un sottoconto.
	 * E' qui perchè viene utilizzato dalle configurazioni e dall'anagrafica
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codconto
	 * @param unknown $codsottoconto
	 * @param unknown $dessottoconto
	 * @return unknown
	 */
	public function inserisciSottoconto($db, $utility, $codconto, $codsottoconto, $dessottoconto) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%cod_sottoconto%' => trim($codsottoconto),
				'%des_sottoconto%' => trim($dessottoconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaSottoconto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}	


	/**
	 * Questo metodo legge tutte le categorie disponibili
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function caricaCategorieCliente($utility, $db) {
	
		$array = $utility->getConfig();
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercacCategorie;
		$sql = $utility->getTemplate($sqlTemplate);
		$result = $db->getData($sql);
	
		foreach(pg_fetch_all($result) as $row) {
	
			if (trim($row['cat_cliente']) == trim($_SESSION["catcliente"])) {
				$elencoCategorieCliente .= "<option value='" . $row['cat_cliente'] . "' selected >" . $row['des_categoria'] . "</option>";
			}
			else {
				$elencoCategorieCliente .= "<option value='" . $row['cat_cliente'] . "'>" . $row['des_categoria'] . "</option>";
			}
		}
		return $elencoCategorieCliente;
	}

	/**
	 * Questo metodo determina l'ambiente sulla bae degli utenti preenti loggati
	 * @param array
	 * @param _SESSION
	 */
	 public function getEnvironment($array) {
	
		$users = shell_exec("who | cut -d' ' -f1 | sort | uniq");
		
		if (strpos($users, $array['usernameProdLogin']) === false) {
			$_SESSION["ambiente"] = "Ambiente di TEST";
		}
		else {
			$_SESSION["ambiente"] = "Ambiente di PRODUZIONE";
		}
	}
}

?>
