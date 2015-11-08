<?php

require_once 'primanota.abstract.class.php';

class CreaIncasso extends primanotaAbstract {

	private static $_instance = null;

	public static $azioneCreaIncasso = "../primanota/creaIncassoFacade.class.php?modo=go";

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

			self::$_instance = new CreaIncasso();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
	
		require_once 'creaIncasso.template.php';
	
		$creaIncassoTemplate = CreaIncassoTemplate::getInstance();
		$this->preparaPagina($creaIncassoTemplate);
	
		// Data del giorno preimpostata solo in entrata -------------------------
	
		$_SESSION["datareg"] = date("d/m/Y");
		$_SESSION["codneg"] = "VIL";
		unset($_SESSION["descreg"]);
		unset($_SESSION["causale"]);
		unset($_SESSION["numfatt"]);
		unset($_SESSION["cliente"]);
		unset($_SESSION["dettagliInseriti"]);
		unset($_SESSION["indexDettagliInseriti"]);
		unset($_SESSION["elenco_scadenze_cliente"]);
	
		// Compone la pagina
		include(self::$testata);
		$creaIncassoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'creaIncasso.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaIncassoTemplate = CreaIncassoTemplate::getInstance();
		if ($creaIncassoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
				
			if ($this->creaIncasso($utility)) {
		
				session_unset();
				$_SESSION["messaggio"] = "Incasso salvato con successo";
				$_SESSION["datareg"] = date("d/m/Y");
				$_SESSION["codneg"] = "VIL";
		
				$this->preparaPagina($creaIncassoTemplate);
		
				include(self::$testata);
				$creaIncassoTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
				
			$this->preparaPagina($creaIncassoTemplate);
		
			include(self::$testata);
			$creaIncassoTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}
		
	public function CreaIncasso($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
		/**
		 * Crea l'incasso
		*/
	
		$descreg = $_SESSION["descreg"];
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$stareg = "00";
		$cliente = ($_SESSION["cliente"] != "") ? $_SESSION["cliente"] : "null" ;
			
		if ($this->inserisciRegistrazione($db, $utility, $descreg, 'null', $datareg, $numfatt, $causale, 'null', $cliente, $codneg, $stareg)) {

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
				$this->cambiaStatoScadenzaCliente($db, $utility, $cliente, $numfatt, '10', $_SESSION['idRegistrazione']);					
			}
			
			/**
			 * Chiudo la registrazione della fattura emessa.
			 * In sessione ho l'ID dell'incasso appena inserito (idRegistrazione), devo recuperare l'ID della
			 * registrazione della fattura originale, lo prendo dalla scadenza che ho appena chiuso
			 */

			$result_scadenza_cliente = $this->leggiScadenzaCliente($db, $utility, $cliente, $_SESSION['idRegistrazione']);
				
			if ($result_scadenza_cliente) {

				foreach(pg_fetch_all($result_scadenza_cliente) as $row) {
					$idregistrazione = $row['id_registrazione'];		// l'id della fattura emessa
				}
				
				$this->cambioStatoRegistrazione($db, $utility, $idregistrazione, '10');		// OK				
			}

			/**
			 * Rigenerazione dei saldi
			 */
			$array = $utility->getConfig();
			
			if ($array['lavoriPianificatiAttivati'] == "Si") {
				$this->rigenerazioneSaldi($db, $utility, strtotime(str_replace('/', '-', $datareg)));
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

	public function preparaPagina($creaIncassoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaIncassoTemplate->setAzione(self::$azioneCreaIncasso);
		$creaIncassoTemplate->setConfermaTip("%ml.confermaCreaIncasso%");
		$creaIncassoTemplate->setTitoloPagina("%ml.creaNuovoIncasso%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per popolare i combo -------------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
	
		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		*/
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);

		/**
		 * Prepara il selectmenu delle scadenze aperte.
		 * Come per i conti, l'ajax non interviene se c'è un errore logico e la pagina viene ripresentata
		 */
		if (isset($_SESSION["idcliente"])) {
			$db = Database::getInstance();
			$utility = Utility::getInstance();
			
			$options = '';
			
			$result_scadenze_cliente = $this->prelevaScadenzeAperteCliente($db, $utility, $_SESSION["idcliente"]);
			
			$d = explode(",", $_SESSION["numfatt"]);
				
			foreach(pg_fetch_all($result_scadenze_cliente) as $row) {
				$options .= '<option value="' . trim($row['num_fattura']) . '" ' . $this->setFatturaSelezionata($d, trim($row['num_fattura'])) . ' >' . trim($row['num_fattura']) . '</option>';
			}
			
			$_SESSION["elenco_scadenze_cliente"] = $options;
		}
		else {
			$_SESSION["elenco_scadenze_cliente"] = "";
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
	