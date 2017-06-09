<?php

abstract class Nexus6Abstract {

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

	// Query ------------------------------------------------------------------------------

	public static $queryRicercaCausali = "/primanota/ricercaCausali.sql";
	public static $queryRicercaFornitori = "/primanota/ricercaFornitori.sql";
	public static $queryRicercaClienti = "/primanota/ricercaClienti.sql";
	public static $queryRicercaConti = "/primanota/ricercaConti.sql";
	public static $queryTrovaDescrizioneFornitore = "/anagrafica/trovaDescFornitore.sql";
	public static $queryCreaEvento = "/main/creaEvento.sql";
	public static $queryChiudiEvento = "/main/chiudiEvento.sql";
	public static $queryCreaSaldo = "/saldi/creaSaldo.sql";
	public static $queryAggiornaSaldo = "/saldi/aggiornaSaldo.sql";
	public static $queryLeggiSaldo = "/saldi/leggiSaldo.sql";
	public static $queryCancellaSaldo = "/saldi/cancellaSaldo.sql";
	public static $queryLavoriPianificati = "/main/lavoriPianificati.sql";
	public static $queryLavoriPianificatiAnnoCorrente = "/main/lavoriPianificatiAnnoCorrente.sql";

	public static $queryLeggiTuttiConti = "/configurazioni/leggiTuttiConti.sql";
	public static $queryRicercaMercati = "/configurazioni/leggiTuttiMercati.sql";
	public static $queryRicercaMercatiNegozio = "/primanota/ricercaMercati.sql";

	public static $queryControllaScadenzeFornitoreSuperate = "/main/controllaScadenzeFornitoreSuperate.sql";
	public static $queryControllaScadenzeClienteSuperate = "/main/controllaScadenzeClienteSuperate.sql";
	public static $queryControllaRegistrazioniInErrore = "/main/controllaRegistrazioniInErrore.sql";

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

	// Composizione del menu in testata pagine --------------------------------------------

	public function makeMenu($utility) : string {

		$array = $utility->getConfig();

		$menu = "<li style='width: 70px;'><img src='../../images/unicorn.png' style='width: 50%'/></li>";;

		// H o m e --------------------------------------

		$home = "";

		if ($array["home"] == "Y") {
			$home .= "<li><a href='../main/start.php'>" . $array['home_menu_title'] . "</a>";
			$home .= "<ul>";
			if ($array["home_item_1"] == "Y") $home .= "<li><a href='../strumenti/cambiaContoStep1Facade.class.php?modo=start'>". $array["home_item_1_name"] . "</a></li>";
			if ($array["home_item_2"] == "Y") $home .= "<li><a href='../strumenti/lavoriAutomaticiFacade.class.php?modo=start'>". $array["home_item_2_name"] . "</a></li>";
			$home .= "</ul></li>";
		}
		$menu .= $home;

		// O p er a z i o n i ------------------------------------------------------------

		$operazioni = "";

		if ($array["operazioni"] == "Y") {
			$operazioni .= "<li><a>" . $array["operazioni_menu_title"] . "</a>";
			$operazioni .= "<ul>";
			if ($array["operazioni_item_1"] == "Y") $operazioni .= "<li><a href='../primanota/ricercaRegistrazioneFacade.class.php?modo=start'>" . $array["operazioni_item_1_name"] . "</a></li>";
			if ($array["operazioni_item_2"] == "Y") $operazioni .= "<li><a href='../primanota/creaRegistrazioneRapidaFacade.class.php?modo=start'>" . $array["operazioni_item_2_name"] . "</a></li>";
			if ($array["operazioni_item_3"] == "Y") $operazioni .= "<li><a href='../primanota/creaRegistrazioneFacade.class.php?modo=start'>" . $array["operazioni_item_3_name"] . "</a></li>";
			if ($array["operazioni_item_4"] == "Y") $operazioni .= "<li><a href='../primanota/creaPagamentoFacade.class.php?modo=start'>" . $array["operazioni_item_4_name"] . "</a></li>";
			if ($array["operazioni_item_5"] == "Y") $operazioni .= "<li><a href='../primanota/creaIncassoFacade.class.php?modo=start'>" . $array["operazioni_item_5_name"] . "</a></li>";
			if ($array["operazioni_item_6"] == "Y") $operazioni .= "<li><a href='../primanota/creaCorrispettivoNegozioFacade.class.php?modo=start'>" . $array["operazioni_item_6_name"] . "</a></li>";
			if ($array["operazioni_item_7"] == "Y") $operazioni .= "<li><a href='../primanota/creaCorrispettivoMercatoFacade.class.php?modo=start'>" . $array["operazioni_item_7_name"] . "</a></li>";
			$operazioni .= "<li><hr/></li>";
			if ($array["operazioni_item_8"] == "Y") $operazioni .= "<li><a href='../primanota/importaExcelCorrispettivoNegozioStep1Facade.class.php?modo=start'>" . $array["operazioni_item_8_name"] . "</a></li>";
			if ($array["operazioni_item_9"] == "Y") $operazioni .= "<li><a href='../primanota/importaExcelCorrispettivoMercatoStep1Facade.class.php?modo=start'>" . $array["operazioni_item_9_name"] . "</a></li>";
			$operazioni .= "</ul></li>";
		}
		$menu .= $operazioni;

		// A n a g r a f i c h e ------------------------------------------------------------

		$anagrafiche = "";

		if ($array["anagrafiche"] == "Y") {
			$anagrafiche .= "<li><a>" . $array["anagrafiche_menu_title"] . "</a>";
			$anagrafiche .= "<ul>";
			if ($array["anagrafiche_item_3"] == "Y") $anagrafiche .= "<li><a href='../anagrafica/ricercaFornitoreFacade.class.php?modo=start'>" . $array["anagrafiche_item_3_name"] . "</a></li>";
			if ($array["anagrafiche_item_4"] == "Y") $anagrafiche .= "<li><a href='../anagrafica/ricercaClienteFacade.class.php?modo=start'>" . $array["anagrafiche_item_4_name"] . "</a></li>";
			if ($array["anagrafiche_item_5"] == "Y") $anagrafiche .= "<li><a href='../anagrafica/ricercaMercatoFacade.class.php?modo=start'>" . $array["anagrafiche_item_5_name"] . "</a></li>";
			$anagrafiche .= "</ul></li>";
		}
		$menu .= $anagrafiche;

		// C o n f i g u r a z i o n i ------------------------------------------------------------

		$configurazioni = "";

		if ($array["configurazioni"] == "Y") {
			$configurazioni .= "<li><a>" . $array["configurazioni_menu_title"] . "</a>";
			$configurazioni .= "<ul>";
			if ($array["configurazioni_item_2"] == "Y") $configurazioni .= "<li><a href='../configurazioni/ricercaContoFacade.class.php?modo=start'>" . $array["configurazioni_item_2_name"] . "</a></li>";
			if ($array["configurazioni_item_4"] == "Y") $configurazioni .= "<li><a href='../configurazioni/ricercaCausaleFacade.class.php?modo=start'>" . $array["configurazioni_item_4_name"] . "</a></li>";
			if ($array["configurazioni_item_5"] == "Y") $configurazioni .= "<li><a href='../configurazioni/ricercaProgressivoFatturaFacade.class.php?modo=start'>" . $array["configurazioni_item_5_name"] . "</a></li>";
			$configurazioni .= "</ul></li>";
		}
		$menu .= $configurazioni;

		// S c a d e n z e ------------------------------------------------------------

		$scadenze = "";

		if ($array["scadenze"] == "Y") {
			$scadenze .= "<li><a>" . $array["scadenze_menu_title"] . "</a>";
			$scadenze .= "<ul>";
			if ($array["scadenze_item_1"] == "Y") $scadenze .= "<li><a href='../scadenze/ricercaScadenzeFornitoreFacade.class.php?modo=start'>" . $array["scadenze_item_1_name"] . "</a></li>";
			if ($array["scadenze_item_2"] == "Y") $scadenze .= "<li><a href='../scadenze/ricercaScadenzeClienteFacade.class.php?modo=start'>" . $array["scadenze_item_2_name"] . "</a></li>";
			$scadenze .= "</ul></li>";
		}
		$menu .= $scadenze;

		// R i e p i o l o g h i ------------------------------------------------------------

		$riepiloghi = "";

		if ($array["riepiloghi"] == "Y") {
			$riepiloghi .= "<li><a>" . $array["riepiloghi_menu_title"] . "</a>";
			$riepiloghi .= "<ul>";
			if ($array["riepiloghi_item_1"] == "Y") $riepiloghi .= "<li><a href='../riepiloghi/bilancioFacade.class.php?modo=start'>" . $array["riepiloghi_item_1_name"] . "</a></li>";
			if ($array["riepiloghi_item_2"] == "Y") $riepiloghi .= "<li><a href='../riepiloghi/bilancioEsercizioFacade.class.php?modo=start'>" . $array["riepiloghi_item_2_name"] . "</a></li>";
			if ($array["riepiloghi_item_3"] == "Y") $riepiloghi .= "<li><a href='../riepiloghi/riepilogoNegoziFacade.class.php?modo=start'>" . $array["riepiloghi_item_3_name"] . "</a></li>";
			if ($array["riepiloghi_item_4"] == "Y") $riepiloghi .= "<li><a href='../riepiloghi/andamentoNegoziFacade.class.php?modo=start'>" . $array["riepiloghi_item_4_name"] . "</a></li>";
			if ($array["riepiloghi_item_7"] == "Y") $riepiloghi .= "<li><a href='../riepiloghi/andamentoNegoziConfrontatoFacade.class.php?modo=start'>" . $array["riepiloghi_item_7_name"] . "</a></li>";
			if ($array["riepiloghi_item_8"] == "Y") $riepiloghi .= "<li><a href='../riepiloghi/andamentoMercatiFacade.class.php?modo=start'>" . $array["riepiloghi_item_8_name"] . "</a></li>";
			$riepiloghi .= "<li><hr/></li>";
			if ($array["riepiloghi_item_5"] == "Y") $riepiloghi .= "<li><a href='../saldi/ricercaSaldiFacade.class.php?modo=start'>" . $array["riepiloghi_item_5_name"] . "</a></li>";
			if ($array["riepiloghi_item_6"] == "Y") $riepiloghi .= "<li><a href='../saldi/creaSaldoFacade.class.php?modo=start'>" . $array["riepiloghi_item_6_name"] . "</a></li>";
			$riepiloghi .= "</ul></li>";
		}
		$menu .= $riepiloghi;

		// F a t t u r e ------------------------------------------------------------

		$fatture = "";

		if ($array["fatture"] == "Y") {
			$fatture .= "<li><a>" . $array["fatture_menu_title"] . "</a>";
			$fatture .= "<ul>";
			if ($array["fatture_item_1"] == "Y") $fatture .= "<li><a href='../fatture/creaFatturaAziendaConsortileFacade.class.php?modo=start'>" . $array["fatture_item_1_name"] . "</a></li>";
			if ($array["fatture_item_2"] == "Y") $fatture .= "<li><a href='../fatture/creaFatturaEntePubblicoFacade.class.php?modo=start'>" . $array["fatture_item_2_name"] . "</a></li>";
			if ($array["fatture_item_3"] == "Y") $fatture .= "<li><a href='../fatture/creaFatturaClienteFacade.class.php?modo=start'>" . $array["fatture_item_2_name"] . "</a></li>";
			$fatture .= "</ul></li>";
		}
		$menu .= $fatture;

		return $menu;
	}

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

		$sqlTemplate = $this->root . $array['query'] . self::$queryRicercaCausali;
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

		$sqlTemplate = $this->root . $array['query'] . self::$queryRicercaFornitori;
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

		$sqlTemplate = $this->root . $array['query'] . self::$queryRicercaClienti;
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

		$sqlTemplate = $this->root . $array['query'] . self::$queryRicercaConti;
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

		$sqlTemplate = $this->root . $array['query'] . self::$queryLeggiTuttiConti;
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

		$sqlTemplate = $this->root . $array['query'] . self::$queryRicercaMercati;
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

		$sqlTemplate = $this->root . $array['query'] . self::$queryRicercaMercatiNegozio;
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
	 * @param unknown $idcliente
	 * @return unknown
	 */
	public function leggiIdCliente($db, $utility, $idcliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente)
		);
		$sqlTemplate = $this->root . $array['query'] . self::$queryLeggiIdCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo recupera l'id di un fornitore accedendo con la descrizione
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $desfornitore
	 * @return string
	 */
	public function leggiDescrizioneFornitore($db, $utility, $desfornitore) : string {

		$array = $utility->getConfig();
		$replace = array(
				'%des_fornitore%' => trim($desfornitore)
		);
		$sqlTemplate = $this->root . $array['query'] . self::$queryTrovaDescrizioneFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		error_log($sql);

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
	 * @return string
	 */
	public function leggiDescrizioneCliente($db, $utility, $descliente) : string {

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
		$sqlTemplate = $this->root . $array['query'] . self::$queryLavoriPianificatiAnnoCorrente;

		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		return $result;
	}




	/**
	 * Questo metodo determina l'ambiente sulla bae degli utenti preenti loggati
	 * @param array
	 * @param _SESSION
	 */
	 public function getEnvironment($array) {

		$users = shell_exec("who | cut -d' ' -f1 | sort | uniq");

		if (strpos($users, $array['usernameProdLogin']) === false) {
			$_SESSION["ambiente"] = "TEST";
		}
		else {
			$_SESSION["ambiente"] = "PROD";
		}
	}

	/**
	 * Questo metodo effettua un controllo sullo scadenziario dei fornitori.
	 * Se ci sono scadenze superate restituisce un testo di notifica
	 *
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function controllaScadenzeFornitoriSuperate($utility, $db) : string {

		$array = $utility->getConfig();
		$replace = array();
		$sqlTemplate = $this->root . $array['query'] . self::$queryControllaScadenzeFornitoreSuperate;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		$scadenze = "";

		foreach(pg_fetch_all($result) as $row) {
			$scadenze .= "&ndash; Pagamento scaduto il " . $row['dat_scadenza'] . " : " . $row['nota_scadenza'] . "<br/>";
		}
		return $scadenze;
	}

	/**
	 * Questo metodo effettua un controllo sullo scadenziario dei clienti.
	 * Se ci sono scadenze superate restituisce un testo di notifica
	 *
	 * @param unknown $utility
	 * @param unknown $db
	 * @return string
	 */
	public function controllaScadenzeClientiSuperate($utility, $db) : string {

		$array = $utility->getConfig();
		$replace = array();
		$sqlTemplate = $this->root . $array['query'] . self::$queryControllaScadenzeClienteSuperate;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		$scadenze = "";

		foreach(pg_fetch_all($result) as $row) {
			$scadenze .= "&ndash; Incasso scaduto il " . $row['dat_registrazione'] . " : " . $row['nota'] . "<br/>";
		}
		return $scadenze;
	}

	public function controllaRegistrazioniInErrore($utility, $db) : string {

		$array = $utility->getConfig();
		$replace = array();
		$sqlTemplate = $this->root . $array['query'] . self::$queryControllaRegistrazioniInErrore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		$scadenze = "";

		foreach(pg_fetch_all($result) as $row) {
			$scadenze .= "&ndash; Operazione errata del " . $row['dat_registrazione'] . " : " . $row['cod_negozio'] . " - " . $row['des_registrazione'] . "<br/>";
		}
		return $scadenze;
	}

}

?>
