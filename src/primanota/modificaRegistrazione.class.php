<?php

require_once 'primanota.abstract.class.php';

class ModificaRegistrazione extends primanotaAbstract {

	private static $_instance = null;
	private static $categoria_causali = 'GENERI';
	
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
		$this->prelevaDatiScadenzeRegistrazione($utility);

		/**
		 * Prelevo in entrata il nome della funzione REFERER e ci estraggo il nome della funzione verso la 
		 * quale redirigere l'utente dopo la modifica 
		 */
		if (!isset($_SESSION['referer_function_name'])) {
			$_SESSION['referer_function_name'] = $_SERVER['HTTP_REFERER'];
		}
		
		$modificaRegistrazioneTemplate = ModificaRegistrazioneTemplate::getInstance();
		$this->preparaPagina($modificaRegistrazioneTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaRegistrazioneTemplate->displayPagina();
		include(self::$piede);	
	}

	public function go() {
	
		require_once 'modificaRegistrazione.template.php';
		require_once 'ricercaRegistrazione.class.php';
		require_once 'ricercaScadenze.class.php';
		require_once 'ricercaScadenzeCliente.class.php';
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

				$_SESSION["messaggioModifica"] = "Registrazione salvata con successo";
				
				/**
				 * Aggiungo al messaggio da emettere una nota per avvisare se è stato rimosso un pagamento
				 * o un incasso associati alla scadenza.
				 */
				
				if (isset($_SESSION["pagamentoCancellato"]) and $_SESSION["pagamentoCancellato"] != "") {				
					$_SESSION["messaggioModifica"] .= "<br/>ATTENZIONE: " . $_SESSION["pagamentoCancellato"]; 
					unset($_SESSION["pagamentoCancellato"]);
				}

				if (isset($_SESSION["incassoCancellato"]) and $_SESSION["incassoCancellato"] != "") {
					$_SESSION["messaggioModifica"] .= "<br/>ATTENZIONE: " . $_SESSION["incassoCancellato"];
					unset($_SESSION["incassoCancellato"]);
				}
				
				$fileClass = $_SESSION['referer_function_name'];

				if (strrpos($fileClass,"ScadenzeCliente") > 0) {	// scadenze clienti
					$ricercaScadenzeCliente = RicercaScadenzeCliente::getInstance();
					$ricercaScadenzeCliente->go();
				}
				elseif (strrpos($fileClass,"Scadenze") > 0) {			// scadenze fornitori
					$ricercaScadenze = RicercaScadenze::getInstance();
					$ricercaScadenze->go();
				}
				elseif (strrpos($fileClass,"Registrazione") > 0) {
					$ricercaRegistrazione = RicercaRegistrazione::getInstance();
					unset($_SESSION["numfatt"]);
					$ricercaRegistrazione->go();						
				}
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
				$_SESSION["datascad_old"] = $row["dat_scadenza"];
				$_SESSION["datareg"] = $row["dat_registrazione"];
				$_SESSION["datareg_old"] = $row["dat_registrazione"];
				$_SESSION["numfatt"] = $row["num_fattura"];
				$_SESSION["codneg"] = $row["cod_negozio"];
				$_SESSION["causale"] = $row["cod_causale"];
				$_SESSION["desforn"] = $row["des_fornitore"];
				$_SESSION["descli"] = $row["des_cliente"];				
				$_SESSION["stascad"] = $row["sta_scadenza"];
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
	
		$descreg = str_replace("'", "''", $_SESSION["descreg"]);
		$datascad = ($_SESSION["datascad"] != "") ? "'" . $_SESSION["datascad"] . "'" : "null" ;
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$stareg = $_SESSION["stareg"];
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];		
		$fornitore = ($_SESSION["fornitore"] != "") ? $this->leggiDescrizioneFornitore($db, $utility, $_SESSION["fornitore"]) : "null" ;
		$cliente = ($_SESSION["cliente"] != "") ? $this->leggiDescrizioneCliente($db, $utility, $_SESSION["cliente"]) : "null" ;		
		$staScadenza = "00"; 
		
		if ($this->updateRegistrazione($db, $utility, $_SESSION["idRegistrazione"], $_SESSION["totaleDare"], $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $stareg, $codneg, $staScadenza)) {

			$importo_in_scadenza = $this->prelevaImportoInScadenza($db, $utility, $fornitore, $cliente);
				
			/**
			 * Se è stata cambiata la data scadenza devo ricreare la scadenza in scadenziario e slegare 
			 * l'eventuale pagamento/incasso associato
			 */
			
			if ($_SESSION["datascad"] != $_SESSION["datascad_old"]) {
								
				if ($fornitore != "null") {
				
					if (!$this->creaScadenzaFornitore($db, $utility, $fornitore, $datascad, $datareg, $causale, $importo_in_scadenza, $descreg, $codneg, $numfatt, $staScadenza)) {
						$db->rollbackTransaction();
						error_log("Errore inserimento scadenza fornitore, eseguito Rollback");
						return FALSE;
					}
				}
				else {
				
					if ($cliente != "null") {
				
						if (!$this->creaScadenzaCliente($db, $utility, $cliente, $datascad, $datareg, $importo_in_scadenza, $descreg, $codneg, $numfatt, $staScadenza)) {
							$db->rollbackTransaction();
							error_log("Errore inserimento scadenza cliente, eseguito Rollback");
							return FALSE;
						}
					}
				}
			}

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

	private function creaScadenzaFornitore($db, $utility, $fornitore, $datascad, $datareg, $causale, $importo_in_scadenza, $descreg, $codneg, $numfatt, $staScadenza) {

		if (isset($_SESSION["numeroScadenzeRegistrazione"])) {
		
			/**
			 * RATIO: se la fattura è multiscadenza vengono aggiornate tutte le scadenze in scadenziario senza cancellarle.
			 * I dati non oggetto di modifica sono: lo stato della scadenza, l'importo della scadenza e le chiavi 
			 */
			
			$scadenze_registrazione = $_SESSION["elencoScadenzeRegistrazione"];
			$progrFattura = 0;
				
			/**
			 * I dati da aggiornare sulle scadenza sono : la nota, il numero fattura, il negozio
			 */
			foreach ($scadenze_registrazione as $row) {
					
				$progrFattura += 1;
				$numfatt_generato = "'" . trim($_SESSION["numfatt"]) . "." . $progrFattura . "'";
				$datascad = ($row["dat_scadenza"] != "") ? "'" . $row["dat_scadenza"] . "'" : "null" ;
				$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		
				$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
				foreach(pg_fetch_all($result_fornitore) as $row) {
					$tipAddebito_fornitore = $row['tip_addebito'];
				}
					
				if ($this->aggiornaScadenza($db, $utility, $row["id_scadenza"], $_SESSION['idRegistrazione'], $datascad, $row["imp_in_scadenza"], $descreg, $tipAddebito_fornitore, $codneg, $fornitore, $numfatt_generato, $row["sta_scadenza"])) {
					return TRUE;
				}
				else return FALSE;
			}
		}
		else {

			/**
			 * RATIO: se la fattura ha una sola data di scadenza, la scadenza viene cancellata e inserita nuovamente con i 
			 * nuovi dati in stato '00'.
			 * 
			 * NOTA : --------------------------------------------------------------------------------------
			 * Se la scadenza ha associato un pagamento allora devo dissociarlo dalla scadenza e rimuoverlo
			 * Se la scadenza non ha associati pagamenti la cancello la scadenza e la ricreo 
			 * ---------------------------------------------------------------------------------------------
			 */

			$_SESSION["pagamentoCancellato"] = "";
			$scadenza = $this->scadenzaFornitore($db, $utility, $_SESSION["idRegistrazione"]);
			
			foreach(pg_fetch_all($scadenza) as $row) {
				if ($row['id_pagamento'] != null) {
					
					error_log("Dissocio il pagamento: " . $row['id_pagamento'] . " dalla registrazione originale");
					if ($this->dissociaPagamentoScadenza($db, $utility, $row['id_scadenza'])) {

						error_log("Cancello il pagamento: " . $row['id_pagamento']);
						if (!$this->cancellaRegistrazione($db, $utility, $row['id_pagamento'])) {
							$db->rollbackTransaction();
							error_log("Errore cancellazione pagamento, eseguito Rollback");
							return FALSE;
						}
						$_SESSION["pagamentoCancellato"] = "Pagamento associato rimosso con successo";						
					}					
					$db->rollbackTransaction();
					error_log("Errore dissociazione pagamento da registrazione originale, eseguito Rollback");
					return FALSE;
				}
			}
			
			error_log("Cancello la scadenza associata alla registrazione: " . $_SESSION["idRegistrazione"]);
			$this->cancellaScadenzaFornitore($db, $utility, $_SESSION["idRegistrazione"]);

			if ($datascad != "null") {
				$data = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
				$dataScadenza = strtotime(str_replace('/', '-', $data));	// cambio i separatori altrimenti la strtotime non funziona
				
				$data1 = str_replace("'", "", $datareg);					// la datareg arriva con gli apici per il db
				$dataRegistrazione = strtotime(str_replace('/', '-', $data1));
				
				if ($dataScadenza > $dataRegistrazione) {
				
					$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
					foreach(pg_fetch_all($result_fornitore) as $row) {
						$tipAddebito_fornitore = $row['tip_addebito'];
					}
				
					error_log("Ricreo la scadenza associata alla registrazione: " . $_SESSION["idRegistrazione"]);
					if (!$this->inserisciScadenza($db, $utility, $_SESSION["idRegistrazione"], $datascad, $importo_in_scadenza, $descreg, $tipAddebito_fornitore, $codneg, $fornitore, trim($numfatt), $staScadenza)) {
						$db->rollbackTransaction();
						error_log("Errore inserimento registrazione, eseguito Rollback");
						return FALSE;
					}
					error_log("Scadenza associata alla registrazione: " . $_SESSION["idRegistrazione"] . " ricreata con successo");
				}				
			}
			return TRUE;
		}
	}

	private function creaScadenzaCliente($db, $utility, $cliente, $datascad, $datareg, $importo_in_scadenza, $descreg, $codneg, $numfatt, $staScadenza) {

		/**
		 * Prima di cancellare la scadenza del cliente devo cancellare l'incasso associato
		 */		
		
		$_SESSION["incassoCancellato"] = "";
		$scadenza = $this->scadenzaCliente($db, $utility, $_SESSION["idRegistrazione"]);
		
		foreach(pg_fetch_all($scadenza) as $row) {
			if ($row['id_incasso'] != '') {
				if (!$this->cancellaRegistrazione($db, $utility, $row['id_incasso'])) {
					$db->rollbackTransaction();
					error_log("Errore cancellazione incasso associato a scadenza, eseguito Rollback");
					return FALSE;
				}
			}
			$_SESSION["incassoCancellato"] = "Incasso associato rimosso";
		}
		
		if ($this->cancellaScadenzaCliente($db, $utility, $_SESSION["idRegistrazione"])) {
			
			$result_cliente = $this->leggiIdCliente($db, $utility, $cliente);
			foreach(pg_fetch_all($result_cliente) as $row) {
				$tipAddebito_cliente = $row['tip_addebito'];
			}
			if (!$this->inserisciScadenzaCliente($db, $utility, $_SESSION['idRegistrazione'], $datareg, $importo_in_scadenza, $descreg, $tipAddebito_cliente, $codneg, $cliente, trim($numfatt), $staScadenza)) {
				$db->rollbackTransaction();
				error_log("Errore inserimento registrazione, eseguito Rollback");
				return FALSE;
			}
			else return TRUE;
		}
		return FALSE;
	}
	
	private function prelevaImportoInScadenza($db, $utility, $fornitore, $cliente) {
		
		$importo_in_scadenza = 0;
		
		$result = $_SESSION["elencoDettagliRegistrazione"];
			
		$dettaglioregistrazione = pg_fetch_all($result);
		$tbodyDettagli = "";
		
		foreach ($dettaglioregistrazione as $row) {
				
			/**
			 * Salvo l'importo inserito sul conto del fornitore o del cliente per inserire la scadenza
			 * I conti relativi ai fornitori/clienti sono in configurazione
			 */
				
			$array = $utility->getConfig();
				
			if ($fornitore != "null") {
				if (strstr($array['contiFornitore'], $row["cod_conto"])) {
					$importo_in_scadenza = $row["imp_registrazione"];
				}
			}
			elseif ($cliente != "null") {
				if (strstr($array['contiCliente'], $row["cod_conto"])) {
					$importo_in_scadenza = $row["imp_registrazione"];
				}
			}
		}
		return $importo_in_scadenza;		
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
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
	}
}	

?>