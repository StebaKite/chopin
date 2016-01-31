<?php

require_once 'primanota.abstract.class.php';

/**
 * Crazione della registrazione base
 * 
 * @author stefano
 *
 */
class CreaRegistrazione extends primanotaAbstract {

	private static $_instance = null;
	private static $categoria_causali = 'GENERI';
	
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
		
		if (!isset($_SESSION["datareg"])) { $_SESSION["datareg"] = date("d/m/Y"); }
		$_SESSION["codneg"] = "";
		
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

				$_SESSION["messaggio"] = "Registrazione salvata con successo";				
				if (!isset($_SESSION["datareg"])) { $_SESSION["datareg"] = date("d/m/Y"); }

				unset($_SESSION["descreg"]);
				unset($_SESSION["datascad"]);
				unset($_SESSION["numfatt"]);
				unset($_SESSION["codneg"]);
				unset($_SESSION["causale"]);
				unset($_SESSION["fornitore"]);
				unset($_SESSION["cliente"]);
				unset($_SESSION["esitoNumeroFattura"]);
				unset($_SESSION["dettagliInseriti"]);
				unset($_SESSION["indexDettagliInseriti"]);
				unset($_SESSION["scadenzeInserite"]);
				unset($_SESSION["indexScadenzeInserite"]);
				
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
		
		$descreg = str_replace("'", "''", $_SESSION["descreg"]);
		$datascad = ($_SESSION["datascad"] != "") ? "'" . $_SESSION["datascad"] . "'" : "null" ;

		/**
		 * Se ci sono più date scadenza inserite viene presa la prima per la registrazione
		 */
		if ($datascad == "null") {
			
			if ($_SESSION['scadenzeInserite'] != "") {
				$d = explode(",", $_SESSION['scadenzeInserite']);
				$e = explode("#",$d[0]);
				$datascad = ($e[1] != "") ? "'" . $e[1] . "'" : "null" ;
			}
		}
		
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$stareg = "00";
		$fornitore = ($_SESSION["fornitore"] != "") ? $this->leggiDescrizioneFornitore($db, $utility, $_SESSION["fornitore"]) : "null" ;
		$cliente = ($_SESSION["cliente"] != "") ? $this->leggiDescrizioneCliente($db, $utility, $_SESSION["cliente"]) : "null" ;
		
		if ($this->inserisciRegistrazione($db, $utility, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $codneg, $stareg)) {

			/**
			 * Se l'aggiornamento della registrazione è andata bene creo le scadenze fornitore o cliente
			 */
					
			if ($_SESSION["fornitore"] != "") {
			
				if ($_SESSION["datascad"] != "") {
						
					$data = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
					$dataScadenza = strtotime(str_replace('/', '-', $data));	// cambio i separatori altrimenti la strtotime non funziona
			
					$data1 = str_replace("'", "", $datareg);					// la datareg arriva con gli apici per il db
					$dataRegistrazione = strtotime(str_replace('/', '-', $data1));
			
					$tipAddebito_fornitore = "";
					$staScadenza = "00"; 	// aperta
			
					if ($dataScadenza > $dataRegistrazione) {
			
						$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
						foreach(pg_fetch_all($result_fornitore) as $row) {
							$tipAddebito_fornitore = $row['tip_addebito'];
						}

						/**
						 *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in modo che venga sottratto al totale
						 *  parziale della data in scadenza
						 */
						 
						$importo_in_scadenza = ($causale == "1110") ? $_SESSION["totaleDare"] * (-1) : $_SESSION["totaleDare"]; 
						
						$this->inserisciScadenza($db, $utility, $_SESSION['idRegistrazione'], $datascad, $importo_in_scadenza,
								$descreg, $tipAddebito_fornitore, $codneg, $fornitore, trim($numfatt), $staScadenza);
					}
				}
				else {

					if ($_SESSION['scadenzeInserite'] != "") {

						$tipAddebito_fornitore = "";
						$staScadenza = "00"; 	// aperta
						
						$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
						foreach(pg_fetch_all($result_fornitore) as $row) {
							$tipAddebito_fornitore = $row['tip_addebito'];
						}
						
						$d = explode(",", $_SESSION['scadenzeInserite']);
						$progrFattura = 0;
						
						foreach($d as $ele) {

							$e = explode("#",$ele);
							$datascad = ($e[1] != "") ? "'" . $e[1] . "'" : "null" ;
							$progrFattura += 1;
							$numfatt_generato = "'" . $_SESSION["numfatt"] . "." . $progrFattura . "'"; 
							
							/**
							 *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in modo che venga sottratto al totale
							 *  parziale della data in scadenza
							 */
								
							$importo_in_scadenza = ($causale == "1110") ? $e[2] * (-1) : $e[2];
								
							$this->inserisciScadenza($db, $utility, $_SESSION['idRegistrazione'], $datascad, $importo_in_scadenza,
									$descreg, $tipAddebito_fornitore, $codneg, $fornitore, $numfatt_generato, $staScadenza);
						}
					}
				}
			}
			else {
				if ($cliente != "null") {
			
					if ($datascad == "null") {			// per i clienti la data scadenza non c'è
			
						$tipAddebito_cliente = "";
						$staScadenza = "00"; 	// aperta
			
						$result_cliente = $this->leggiIdCliente($db, $utility, $cliente);
						foreach(pg_fetch_all($result_cliente) as $row) {
							$tipAddebito_cliente = $row['tip_addebito'];
						}
			
						$this->inserisciScadenzaCliente($db, $utility, $_SESSION['idRegistrazione'], $datareg, $_SESSION["totaleDare"],
								$descreg, $tipAddebito_cliente, $codneg, $cliente, trim($numfatt), $staScadenza);
					}
				}
			}
			
			/**
			 * Creo i dettagli della registrazione passati dalla pagina
			 */
			
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
			 * Rigenerazione dei saldi
			 */
			$array = $utility->getConfig();
				
			if ($array['lavoriPianificatiAttivati'] == "Si") {
				$this->rigenerazioneSaldi($db, $utility, $dataRegistrazione);
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

		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		
		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		 */
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
	}	
}

?>