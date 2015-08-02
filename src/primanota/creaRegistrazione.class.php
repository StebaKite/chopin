<?php

require_once 'primanota.abstract.class.php';

class creaRegistrazione extends primanotaAbstract {
	
	public static $azioneCreaRegistrazione = "../primanota/creaRegistrazioneFacade.class.php?modo=go";
	
	function __construct() {
	
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	
		require_once 'utility.class.php';
	
		$utility = new utility();
		$array = $utility->getConfig();
	
		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];	
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'creaRegistrazione.template.php';
	
		$creaRegistrazioneTemplate = new creaRegistrazioneTemplate();
		$this->preparaPagina($creaRegistrazioneTemplate);
	
		// Compone la pagina
		include(self::$testata);
		$creaRegistrazioneTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {

		require_once 'creaRegistrazione.template.php';
		require_once 'utility.class.php';

		$utility = new utility();
		
		$creaRegistrazioneTemplate = new creaRegistrazioneTemplate();
		
		if ($creaRegistrazioneTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------
			
			if ($this->creaRegistrazione($utility)) {
				
				$_SESSION["messaggio"] = "Registrazione salvata con successo";
				
				unset($_SESSION["descreg"]);
				unset($_SESSION["datascad"]);
				unset($_SESSION["numfatt"]);
				unset($_SESSION["causale"]);
				unset($_SESSION["fornitore"]);
				unset($_SESSION["cliente"]);
				unset($_SESSION["dettagliInseriti"]);				
				unset($_SESSION["indexDettagliInseriti"]);
				
				$this->preparaPagina($creaRegistrazioneTemplate);
				
				include(self::$testata);
				$creaRegistrazioneTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);					
			}
		}
		else {
			
			$this->preparaPagina($creaRegistrazioneTemplate);
				
			include(self::$testata);			
			$creaRegistrazioneTemplate->displayPagina();
				
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);			
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);				
		}		
	}

	public function creaRegistrazione($utility) {

		require_once 'database.class.php';
		
		$db = new database();
		$db->beginTransaction();

		/**
		 * Crea la registrazione e tutti i suoi dettagli
		 */
		
		$descreg = $_SESSION["descreg"];
		$datascad = ($_SESSION["datascad"] != "") ? "'" . $_SESSION["datascad"] . "'" : "null" ;
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$fornitore = ($_SESSION["fornitore"] != "") ? $_SESSION["fornitore"] : "null" ;
		$cliente = ($_SESSION["cliente"] != "") ? $_SESSION["cliente"] : "null" ;
		
		if ($this->inserisciRegistrazione($db, $utility, $descreg, $datascad, $numfatt, $causale, $fornitore, $cliente)) {

 			$d = explode(",", $_SESSION['dettagliInseriti']);

			foreach($d as $ele) {
			
				$e = explode("#",$ele);
				
				$conto = substr($e[0], 0, 3);
				$sottoConto = substr($e[0], 4, 2);
				$importo = $e[1];
				$d_a = $e[2];
								
				if (!$this->inserisciDettaglioRegistrazione($db, $utility, $_SESSION['idRegistrazione'], $conto, $sottoConto, $importo, $d_a)) {
					$db->rollbackTransaction();
					error_log("Errore inserimento dettaglio registrazione, eseguito Rollback");
					return FALSE;
				}
			}

			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento registrazione, eseguito Rollback");
		return FALSE;
	}
	
	
	public function preparaPagina($creaRegistrazioneTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';	
		
		$creaRegistrazioneTemplate->setAzione(self::$azioneCreaRegistrazione);
		$creaRegistrazioneTemplate->setConfermaTip("%ml.confermaCreaRegistrazione%");
		$creaRegistrazioneTemplate->setTitoloPagina("%ml.creaNuovaRegistrazione%");
		
		$db = new database();
		$utility = new utility();
		
		// Prelievo delle causali  -------------------------------------------------------------

		if (!isset($_SESSION['elenco_causali'])) {
			$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		}

		// Prelievo dei fornitori  -------------------------------------------------------------
		
		if (!isset($_SESSION['elenco_fornitori'])) {
			$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		}

		// Prelievo dei clienti  -------------------------------------------------------------
		
		if (!isset($_SESSION['elenco_clienti'])) {
			$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		}
		
		// Prelievo dei conti ------------------------------------------------------------------

		if (!isset($_SESSION['elenco_conti'])) {
			$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
		}
		
		
	}	
}
?>