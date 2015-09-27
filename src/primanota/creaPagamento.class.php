<?php

require_once 'primanota.abstract.class.php';

class CreaPagamento extends primanotaAbstract {

	private static $_instance = null;

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
	
		$creaPagamentoTemplate = CreaPagamentoTemplate::getInstance();
		$this->preparaPagina($creaPagamentoTemplate);
	
		// Data del giorno preimpostata solo in entrata -------------------------
	
		$_SESSION["datareg"] = date("d/m/Y");
		$_SESSION["codneg"] = "VIL";
	
		// Compone la pagina
		include(self::$testata);
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
		
				session_unset();
				$_SESSION["messaggio"] = "Pagamento salvato con successo";
				$_SESSION["datareg"] = date("d/m/Y");
				$_SESSION["codneg"] = "VIL";
		
				$this->preparaPagina($creaPagamentoTemplate);
		
				include(self::$testata);
				$creaPagamentoTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
				
			$this->preparaPagina($creaPagamentoTemplate);
		
			include(self::$testata);
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
	
		$descreg = $_SESSION["descreg"];
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$stareg = "00";
		$fornitore = ($_SESSION["fornitore"] != "") ? $_SESSION["fornitore"] : "null" ;
			
		if ($this->inserisciRegistrazione($db, $utility, $descreg, 'null', $datareg, $numfatt, $causale, $fornitore, 'null', $codneg, $stareg)) {

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
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
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
			
			$result_scadenze_fornitore = $this->prelevaScadenzeAperteFornitore($db, $utility, $_SESSION["idfornitore"]);
			
			$d = explode(",", $_SESSION["numfatt"]);
				
			foreach(pg_fetch_all($result_scadenze_fornitore) as $row) {
				$options .= '<option value="' . trim($row['num_fattura']) . '" ' . $this->setFatturaSelezionata($d, trim($row['num_fattura'])) . ' >' . trim($row['num_fattura']) . '</option>';
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
	