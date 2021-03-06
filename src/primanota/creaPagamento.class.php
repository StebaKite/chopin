<?php

require_once 'primanota.abstract.class.php';

class CreaPagamento extends PrimanotaAbstract {

	private static $_instance = null;
	private static $categoria_causali = 'GENERI';
	
	public static $azioneCreaPagamento = "../primanota/creaPagamentoFacade.class.php?modo=go";

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CreaPagamento();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
	
		require_once 'creaPagamento.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaPagamentoTemplate = CreaPagamentoTemplate::getInstance();
		$this->preparaPagina($creaPagamentoTemplate);
	
		// Data del giorno preimpostata solo in entrata -------------------------
	
		if (!isset($_SESSION["datareg"])) { $_SESSION["datareg"] = date("d/m/Y"); }
		unset($_SESSION["descreg"]);
		unset($_SESSION["numfatt"]);
		unset($_SESSION["codneg"]);
		unset($_SESSION["causale"]);
		unset($_SESSION["fornitore"]);
		unset($_SESSION["desforn"]);
		unset($_SESSION["dettagliInseriti"]);
		unset($_SESSION["indexDettagliInseriti"]);
		unset($_SESSION["elenco_scadenze_cliente"]);
		
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaPagamentoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'creaPagamento.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaPagamentoTemplate = CreaPagamentoTemplate::getInstance();
		if ($creaPagamentoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
				
			if ($this->creaPagamento($utility)) {
		
				$_SESSION["messaggio"] = "Pagamento salvato con successo";
				
				if (!isset($_SESSION["datareg"])) { $_SESSION["datareg"] = date("d/m/Y"); }
				unset($_SESSION["descreg"]);
				unset($_SESSION["numfatt"]);
				unset($_SESSION["codneg"]);
				unset($_SESSION["causale"]);
				unset($_SESSION["fornitore"]);
				unset($_SESSION["desforn"]);
				unset($_SESSION["dettagliInseriti"]);
				unset($_SESSION["indexDettagliInseriti"]);
				unset($_SESSION["elenco_scadenze_cliente"]);
		
				$this->preparaPagina($creaPagamentoTemplate);
		
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);
				
				$creaPagamentoTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
				
			$this->preparaPagina($creaPagamentoTemplate);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
			
			$creaPagamentoTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}
		
	public function CreaPagamento($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
		/**
		 * Crea il pagamento
		*/
	
		$descreg = str_replace("'", "''", $_SESSION["descreg"]);
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$stareg = "00";
		$fornitore = ($_SESSION["idfornitore"] != "") ? $_SESSION["idfornitore"] : "null" ;
			
		if ($this->inserisciRegistrazione($db, $utility, $descreg, 'null', $datareg, $numfatt, $causale, $fornitore, 'null', $codneg, $stareg, 'null')) {

			$d = explode(",", $_SESSION['dettagliInseriti']);
			
			foreach($d as $ele) {
					
				$e = explode("#",$ele);
				$cc = explode(" - ", $e[0]);
			
				$conto = substr(trim($cc[0]), 0, 3);
				$sottoConto = substr(trim($cc[0]), 3);
				$importo = $e[1];
				$d_a = $e[2];
			
				if (!$this->inserisciDettaglioRegistrazione($db, $utility, $_SESSION['idRegistrazione'], $conto, $sottoConto, $importo, $d_a)) {
					$db->rollbackTransaction();
					
					error_log("Errore inserimento dettaglio registrazione, eseguito Rollback");
					return FALSE;
				}
			}
				
			/**
			 * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
			 */
			
			$d = explode(",", $_SESSION["numfatt"]);
	
			foreach($d as $numeroFattura) {
				$numfatt = ($numeroFattura != "") ? "'" . $numeroFattura . "'" : "null" ;
				$this->cambiaStatoScadenzaFornitore($db, $utility, $fornitore, $numfatt, '10', $_SESSION['idRegistrazione']);					
			}

			/**
			 * Chiudo la registrazione della fattura emessa.
			 * In sessione ho l'ID del pagamento appena inserito (idRegistrazione), devo recuperare l'ID della
			 * registrazione della fattura originale, lo prendo dalla scadenza che ho appena chiuso
			 */
			
// 			$result_scadenza_fornitore = $this->leggiScadenzaFornitore($db, $utility, $fornitore, $_SESSION['idRegistrazione']);
			
// 			if ($result_scadenza_fornitore) {
			
// 				foreach(pg_fetch_all($result_scadenza_fornitore) as $row) {
// 					$idregistrazione = $row['id_registrazione'];		// l'id della fattura emessa
// 				}
// 				$this->cambioStatoRegistrazione($db, $utility, $idregistrazione, '10');		// OK
// 			}
			
			/**
			 * Rigenerazione dei saldi
			 */
			$array = $utility->getConfig();
			
			if ($array['lavoriPianificatiAttivati'] == "Si") {
				$this->rigenerazioneSaldi($db, $utility, strtotime(str_replace('/', '-', str_replace("'", "", $datareg))));
			}
				
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore inserimento registrazione, eseguito Rollback");
			return FALSE;
		}
	}

	public function preparaPagina($creaPagamentoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaPagamentoTemplate->setAzione(self::$azioneCreaPagamento);
		$creaPagamentoTemplate->setConfermaTip("%ml.confermaCreaPagamento%");
		$creaPagamentoTemplate->setTitoloPagina("%ml.creaNuovoPagamento%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per popolare i combo -------------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
	
		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		*/
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);

		/**
		 * Prepara il selectmenu delle scadenze aperte.
		 * Come per i conti, l'ajax non interviene se c'è un errore logico e la pagina viene ripresentata
		 */
		if (isset($_SESSION["idfornitore"])) {
			
			$db = Database::getInstance();
			$utility = Utility::getInstance();
			
			$options = '';

			if (!is_numeric($_SESSION["idfornitore"])) {
				$db->beginTransaction();
				$_SESSION["idfornitore"] = $this->leggiDescrizioneFornitore($db, $utility, str_replace("'", "''", $_SESSION["fornitore"]));
				$db->commitTransaction();
			}
			
			$result_scadenze_fornitore = $this->prelevaScadenzeAperteFornitore($db, $utility, $_SESSION["idfornitore"]);
			
			$d = explode(",", $_SESSION["numfatt"]);
				
			foreach(pg_fetch_all($result_scadenze_fornitore) as $row) {
				$options .= '<option value="' . trim($row['num_fattura']) . '" ' . $this->setFatturaSelezionata($d, trim($row['num_fattura'])) . '>Ft.' . trim($row['num_fattura']) . ' - &euro; ' . trim($row['imp_in_scadenza']) . ' - (' . trim($row['nota_scadenza']) . ')</option>';
			}
			
			$_SESSION["elenco_scadenze_fornitore"] = $options;
		}
		else {
			$_SESSION["elenco_scadenze_fornitore"] = "";
		}		
	}
	
	public function setFatturaSelezionata($fattureSelezionate, $numFatt) {
		
		$selected = "";
		
		foreach($fattureSelezionate as $numeroFattura) {
			if ($numeroFattura == $numFatt) {
				$selected = "selected";
				break;
			}
		}
		return $selected;		
	}	
}
	
?>	
	