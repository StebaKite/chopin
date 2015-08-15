<?php

require_once 'primanota.abstract.class.php';

class CreaRegistrazione extends primanotaAbstract {

	private static $_instance = null;
	
	public static $azioneCreaRegistrazione = "../primanota/creaRegistrazioneFacade.class.php?modo=go";
	
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
	
			self::$_instance = new CreaRegistrazione();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'creaRegistrazione.template.php';
	
		$creaRegistrazioneTemplate = CreaRegistrazioneTemplate::getInstance();
		$this->preparaPagina($creaRegistrazioneTemplate);

		// Data del giorno preimpostata solo in entrata -------------------------
		
		$_SESSION["datareg"] = date("d-m-Y");
		
		// Compone la pagina
		include(self::$testata);
		$creaRegistrazioneTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {

		require_once 'creaRegistrazione.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		
		$creaRegistrazioneTemplate = CreaRegistrazioneTemplate::getInstance();
		
		if ($creaRegistrazioneTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------
			
			if ($this->creaRegistrazione($utility)) {

				session_unset();
				$_SESSION["messaggio"] = "Registrazione salvata con successo";				
				$_SESSION["datareg"] = date("d-m-Y");
				
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
				
		$db = Database::getInstance();
		$db->beginTransaction();

		/**
		 * Crea la registrazione e tutti i suoi dettagli
		 */
		
		$descreg = $_SESSION["descreg"];
		$datascad = ($_SESSION["datascad"] != "") ? "'" . $_SESSION["datascad"] . "'" : "null" ;
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$fornitore = ($_SESSION["fornitore"] != "") ? $_SESSION["fornitore"] : "null" ;
		$cliente = ($_SESSION["cliente"] != "") ? $_SESSION["cliente"] : "null" ;
		
		if ($this->inserisciRegistrazione($db, $utility, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente)) {

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
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo dei dati per popolare i combo -------------------------------------------------------------

		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);		
	}	
}
?>