<?php

require_once 'primanota.abstract.class.php';

class ModificaRegistrazione extends primanotaAbstract {

	private static $_instance = null;

	public static $azioneModificaRegistrazione = "../primanota/modificaRegistrazioneFacade.class.php?modo=go";
	
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

			self::$_instance = new ModificaRegistrazione();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaRegistrazione.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$this->prelevaDatiRegistrazione($utility);
		$this->prelevaDatiDettagliRegistrazione($utility);
		
		$modificaRegistrazioneTemplate = ModificaRegistrazioneTemplate::getInstance();
		$this->preparaPagina($modificaRegistrazioneTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaRegistrazioneTemplate->displayPagina();
		include(self::$piede);	
	}

	public function go() {
	
		require_once 'modificaRegistrazione.template.php';
		require_once 'utility.class.php';
	
		/**
		 * Crea una array dei dettagli per il controllo di quadratura
		 */
		$utility = Utility::getInstance();
		$this->prelevaDatiDettagliRegistrazione($utility);
		
		$result = $_SESSION["elencoDettagliRegistrazione"];
		
		$dettaglioregistrazione = pg_fetch_all($result);
		$dett = "";
		
		foreach ($dettaglioregistrazione as $row) {
			$dett = $dett . trim($row["cod_conto"]) . trim($row["cod_sottoconto"]) . "#" . trim($row["imp_registrazione"]) . "#" . trim($row["ind_dareavere"]) . ",";			
		}
		$_SESSION['dettagliInseriti'] = $dett;
		
		$modificaRegistrazioneTemplate = ModificaRegistrazioneTemplate::getInstance();
		
		if ($modificaRegistrazioneTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------
				
			if ($this->aggiornaRegistrazione($utility)) {
				
				$_SESSION["messaggio"] = "Registrazione salvata con successo";

				$this->preparaPagina($modificaRegistrazioneTemplate);
				
				include(self::$testata);
				$modificaRegistrazioneTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
			
			$this->aggiornaStatoRegistrazione($utility);			// mette le registrazione in stato 02 (Errata)	
			$this->preparaPagina($modificaRegistrazioneTemplate);
		
			include(self::$testata);
			$modificaRegistrazioneTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}	
	
	public function prelevaDatiRegistrazione($utility) {
		
		require_once 'database.class.php';
		
		$db = Database::getInstance();
		
		$result = $this->leggiRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);
		
		if ($result) {

			$registrazione = pg_fetch_all($result);				
			foreach ($registrazione as $row) {
				
				$_SESSION["descreg"] = $row["des_registrazione"]; 
				$_SESSION["datascad"] = $row["dat_scadenza"];
				$_SESSION["datareg"] = $row["dat_registrazione"];
				$_SESSION["numfatt"] = $row["num_fattura"];
				$_SESSION["codneg"] = $row["cod_negozio"];
				$_SESSION["causale"] = $row["cod_causale"];
				$_SESSION["fornitore"] = $row["id_fornitore"];
				$_SESSION["cliente"] = $row["id_cliente"];				
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati registrazione : " . $_SESSION["idRegistrazione"] . " <<<<<<<<" );
		}
	}
	
	public function prelevaDatiDettagliRegistrazione($utility) {

		require_once 'database.class.php';
		
		$db = Database::getInstance();
		
		$result = $this->leggiDettagliRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);
		
		if ($result) {		
			$_SESSION["elencoDettagliRegistrazione"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo dati registrazione (dettagli) : " . $_SESSION["idRegistrazione"] . " <<<<<<<<" );
		}		
	}

	public function aggiornaStatoRegistrazione($utility) {
		
		require_once 'database.class.php';
		
		$db = Database::getInstance();
		$this->updateStatoRegistrazione($db, $utility, $_SESSION["idRegistrazione"], $_SESSION["stareg"]);
	}
	
	public function aggiornaRegistrazione($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
		/**
		 * Crea la registrazione e tutti i suoi dettagli
		*/
		
		$descreg = $_SESSION["descreg"];
		$datascad = ($_SESSION["datascad"] != "") ? "'" . $_SESSION["datascad"] . "'" : "null" ;
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$stareg = $_SESSION["stareg"];
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$fornitore = ($_SESSION["fornitore"] != "") ? $_SESSION["fornitore"] : "null" ;
		$cliente = ($_SESSION["cliente"] != "") ? $_SESSION["cliente"] : "null" ;		
	
		if ($this->updateRegistrazione($db, $utility, $_SESSION["idRegistrazione"], $_SESSION["totaleDare"], $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $stareg, $codneg)) {
	
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore inserimento registrazione, eseguito Rollback");
			return FALSE;
		}
	}

	public function preparaPagina($modificaRegistrazioneTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaRegistrazioneTemplate->setAzione(self::$azioneModificaRegistrazione);
		$modificaRegistrazioneTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaRegistrazioneTemplate->setTitoloPagina("%ml.modificaRegistrazione%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per i combo --------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
	}
}	

?>