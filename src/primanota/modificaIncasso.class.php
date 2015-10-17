<?php

require_once 'primanota.abstract.class.php';

class ModificaIncasso extends primanotaAbstract {

	private static $_instance = null;

	public static $azioneModificaIncasso = "../primanota/modificaIncassoFacade.class.php?modo=go";

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

			self::$_instance = new ModificaIncasso();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaIncasso.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		$this->prelevaDatiIncasso($utility);
		$this->prelevaDatiDettagliIncasso($utility);

		/**
		 * Prelevo in entrata il nome della funzione REFERER e ci estraggo il nome della funzione verso la
		 * quale redirigere l'utente dopo la modifica
		 */
		$_SESSION['referer_function_name'] = $_SERVER['HTTP_REFERER'];
		
		$modificaIncassoTemplate = ModificaIncassoTemplate::getInstance();
		$this->preparaPagina($modificaIncassoTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaIncassoTemplate->displayPagina();
		include(self::$piede);		
	}

	public function go() {

		require_once 'modificaIncasso.template.php';
		require_once 'ricercaScadenzeCliente.class.php';
		require_once 'utility.class.php';
		
		/**
		 * Crea una array dei dettagli per il controllo di quadratura
		 */
		$utility = Utility::getInstance();
		$this->prelevaDatiDettagliIncasso($utility);
		
		$result = $_SESSION["elencoDettagliIncasso"];
		
		$dettaglioIncasso = pg_fetch_all($result);
		$dett = "";
		
		foreach ($dettaglioIncasso as $row) {
			$dett = $dett . trim($row["cod_conto"]) . trim($row["cod_sottoconto"]) . "#" . trim($row["imp_registrazione"]) . "#" . trim($row["ind_dareavere"]) . ",";
		}
		$_SESSION['dettagliInseriti'] = $dett;
		
		$modificaIncassoTemplate = ModificaIncassoTemplate::getInstance();
		
		if ($modificaIncassoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->aggiornaIncasso($utility)) {
		
				$_SESSION["messaggioModifica"] = "Incasso salvato con successo";

				$fileClass = $_SESSION['referer_function_name'];
				
				if (strrpos($fileClass,"ScadenzeCliente") > 0) {
					$ricercaScadenzeCliente = RicercaScadenzeCliente::getInstance();
					$ricercaScadenzeCliente->go();
				}
			}
		}
		else {
				
			$this->preparaPagina($modificaIncassoTemplate);
		
			include(self::$testata);
			$modificaIncassoTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}
	
	public function prelevaDatiIncasso($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiRegistrazione($db, $utility, $_SESSION["idIncasso"]);
	
		if ($result) {
	
			$incasso = pg_fetch_all($result);
			foreach ($incasso as $row) {
	
				$_SESSION["descreg"] = $row["des_registrazione"];
				$_SESSION["datareg"] = $row["dat_registrazione"];
				$_SESSION["numfatt"] = $row["num_fattura"];
				$_SESSION["numfatt_old"] = $row["num_fattura"];
				$_SESSION["codneg"] = $row["cod_negozio"];
				$_SESSION["causale"] = $row["cod_causale"];
				$_SESSION["cliente"] = $row["id_cliente"];
				$_SESSION["cliente_old"] = $row["id_cliente"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati incasso : " . $_SESSION["idIncasso"] . " <<<<<<<<" );
		}
	}
	
	public function prelevaDatiDettagliIncasso($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiDettagliRegistrazione($db, $utility, $_SESSION["idIncasso"]);
	
		if ($result) {
			$_SESSION["elencoDettagliIncasso"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo dati incasso (dettagli) : " . $_SESSION["idIncasso"] . " <<<<<<<<" );
		}
	}
	
	public function aggiornaIncasso($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
		
		/**
		 * Aggiornamento della registrazione di incasso
		 */		
		
		$descreg = $_SESSION["descreg"];
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$stareg = $_SESSION["stareg"];
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$cliente = ($_SESSION["cliente"] != "") ? $_SESSION["cliente"] : "null" ;
		$staScadenza = "10";   // pagata
		
		if ($this->updateRegistrazione($db, $utility, $_SESSION["idIncasso"], $_SESSION["totaleDare"],
			$descreg, 'null', $datareg, $numfatt, $causale, 'null', $cliente, $stareg,
			$codneg, $staScadenza)) {

			/**
			 * Se sono cambiati il codice cliente o i numeri fattura, cambio lo stato alle fatture incassate: da "10" a "00"
			 * e riporto la registrazione originale a '00', valorizzo a null anche l'id_incasso
			 */
			
			if ((trim($_SESSION["cliente"]) != trim($_SESSION["cliente_old"])) || (trim($_SESSION["numfatt"]) != trim($_SESSION["numfatt_old"]))) {
			
				$d = explode(",", $_SESSION["numfatt_old"]);
					
				foreach($d as $numeroFattura) {
					$numfatt = ($numeroFattura != "") ? "'" . $numeroFattura . "'" : "null" ;
					$this->cambiaStatoScadenzaCliente($db, $utility, $_SESSION["cliente_old"], $numfatt, '00', 'null');
					
					$result_idReg = $this->prelevaIdRegistrazioneOriginaleCliente($db, $utility, $_SESSION["cliente_old"], $numfatt);
					
					if ($result_idReg) {
					
						foreach(pg_fetch_all($result_idReg) as $row) {
							$idregistrazione = $row['id_registrazione'];		// l'id della fattura originale
						}					
						$this->cambioStatoRegistrazione($db, $utility, $idregistrazione, '00');
					}
				}
				
				/**
				 * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
				 */
				
				$d = explode(",", $_SESSION["numfatt"]);
					
				foreach($d as $numeroFattura) {
					$numfatt = ($numeroFattura != "") ? "'" . $numeroFattura . "'" : "null" ;
					$this->cambiaStatoScadenzaCliente($db, $utility, $cliente, $numfatt, '10', $_SESSION['idIncasso']);

					$result_idReg = $this->prelevaIdRegistrazioneOriginaleCliente($db, $utility, $_SESSION["cliente"], $numfatt);
						
					if ($result_idReg) {
							
						foreach(pg_fetch_all($result_idReg) as $row) {
							$idregistrazione = $row['id_registrazione'];		// l'id della fattura originale
						}
						$this->cambioStatoRegistrazione($db, $utility, $idregistrazione, '10');
					}
				}
			}
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento incasso, eseguito Rollback");
			return FALSE;
		}
	}
		
	public function preparaPagina($modificaIncassoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaIncassoTemplate->setAzione(self::$azioneModificaIncasso);
		$modificaIncassoTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaIncassoTemplate->setTitoloPagina("%ml.modificaIncasso%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per i combo --------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);

		/**
		 * Prepara il selectmenu delle scadenze aperte.
		 * Come per i conti, l'ajax non interviene se c'è un errore logico e la pagina viene ripresentata
		 */
		if (isset($_SESSION["cliente"])) {
			$db = Database::getInstance();
			$utility = Utility::getInstance();
				
			$options = '';
				
			$result_scadenze_cliente = $this->prelevaScadenzeCliente($db, $utility, $_SESSION["cliente"], $_SESSION["idRegistrazione"]);
				
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
			if (trim($numeroFattura) == trim($numFatt)) {
				$selected = "selected";
				break;
			}
		}
		return $selected;
	}	
}

?>