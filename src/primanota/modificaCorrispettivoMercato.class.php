<?php

require_once 'primanota.abstract.class.php';

class ModificaCorrispettivoMercato extends primanotaAbstract {

	private static $_instance = null;
	private static $categoria_causali = 'GENERI';
	
	public static $azioneModificaCorrispettivo = "../primanota/modificaCorrispettivoMercatoFacade.class.php?modo=go";
		
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
			self::$_instance = new ModificaCorrispettivoMercato();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaCorrispettivoMercato.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$this->prelevaDatiRegistrazione($utility);
		$this->prelevaDatiDettagliRegistrazione($utility);

		/**
		 * Prelevo in entrata il nome della funzione REFERER e ci estraggo il nome della funzione verso la 
		 * quale redirigere l'utente dopo la modifica 
		 */
		if (!isset($_SESSION['referer_function_name'])) {
			$_SESSION['referer_function_name'] = $_SERVER['HTTP_REFERER'];
		}
		
		$modificaCorrispettivoMercatoTemplate = ModificaCorrispettivoMercatoTemplate::getInstance();
		$this->preparaPagina($modificaCorrispettivoMercatoTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaCorrispettivoMercatoTemplate->displayPagina();
		include(self::$piede);	
	}

	public function go() {
	
		require_once 'modificaCorrispettivoMercato.template.php';
		require_once 'ricercaRegistrazione.class.php';
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
		
		$modificaCorrispettivoMercatoTemplate = ModificaCorrispettivoMercatoTemplate::getInstance();
		
		if ($modificaCorrispettivoMercatoTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------
				
			if ($this->aggiornaRegistrazione($utility)) {

				$_SESSION["messaggioModifica"] = "Registrazione salvata con successo";
								
				$fileClass = $_SESSION['referer_function_name'];

				if (strrpos($fileClass,"Registrazione") > 0) {
					$ricercaRegistrazione = RicercaRegistrazione::getInstance();
					unset($_SESSION["numfatt"]);
					$ricercaRegistrazione->go();						
				}
			}
		}
		else {
			
			$this->aggiornaStatoRegistrazione($utility);			// mette le registrazione in stato 02 (Errata)	
			$this->preparaPagina($modificaCorrispettivoMercatoTemplate);
		
			include(self::$testata);
			$modificaCorrispettivoMercatoTemplate->displayPagina();
		
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
				$_SESSION["datareg"] = $row["dat_registrazione"];
				$_SESSION["datareg_old"] = $row["dat_registrazione"];
				$_SESSION["codneg"] = $row["cod_negozio"];
				$_SESSION["causale"] = $row["cod_causale"];
				$_SESSION["idmercato"] = $row["id_mercato"];
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
		$rimuoviOperazioneAssociata = false;
	
		$descreg = str_replace("'", "''", $_SESSION["descreg"]);
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$stareg = $_SESSION["stareg"];
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];		
		$staScadenza = "00"; 
		$idmercato = $_SESSION["idmercato"];
		
		if ($this->updateRegistrazione($db, $utility, $_SESSION["idRegistrazione"], $_SESSION["totaleDare"], $descreg, 'null', $datareg, 'null', $causale, 'null', 'null', $stareg, $codneg, $staScadenza, $idmercato)) {

			/**
			 * Rigenero i saldi
			 */
			
			$array = $utility->getConfig();
				
			if ($array['lavoriPianificatiAttivati'] == "Si") {
				
				/**
				 * Se è cambiata la data di registrazione devo rigenerare i saldi due volte per le due date altrimenti
				 * i riporti dei saldi potrebbero contenere l'importo due volte
				 * Questo potrebbe accadere se la data registrazione viene portata da un mese all'altro
				 */
				
				$datareg_new = strtotime(str_replace('/', '-', str_replace("'", "", $datareg)));
				$datareg_old = strtotime(str_replace('/', '-', $_SESSION["datareg_old"]));
				if ($datareg_new != $datareg_old) {
					$this->rigenerazioneSaldi($db, $utility, $datareg_old);						
				}
				$this->rigenerazioneSaldi($db, $utility, $datareg_new);
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
	
	public function preparaPagina($modificaCorrispettivoMercatoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaCorrispettivoMercatoTemplate->setAzione(self::$azioneModificaCorrispettivo);
		$modificaCorrispettivoMercatoTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaCorrispettivoMercatoTemplate->setTitoloPagina("%ml.modificaCorrispettivoMercato%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per i combo --------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
		$_SESSION['elenco_mercati'] = $this->caricaMercatiNegozio($utility, $db);
	}
}	

?>