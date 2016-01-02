<?php

require_once 'primanota.abstract.class.php';

class ModificaPagamento extends primanotaAbstract {

	private static $_instance = null;
	private static $categoria_causali = 'INCPAG';
	
	public static $azioneModificaPagamento = "../primanota/modificaPagamentoFacade.class.php?modo=go";

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

			self::$_instance = new ModificaPagamento();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaPagamento.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		$this->prelevaDatiPagamento($utility);
		$this->prelevaDatiDettagliPagamento($utility);

		/**
		 * Prelevo in entrata il nome della funzione REFERER e ci estraggo il nome della funzione verso la
		 * quale redirigere l'utente dopo la modifica
		 */
		$_SESSION['referer_function_name'] = $_SERVER['HTTP_REFERER'];
		
		$modificaPagamentoTemplate = ModificaPagamentoTemplate::getInstance();
		$this->preparaPagina($modificaPagamentoTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaPagamentoTemplate->displayPagina();
		include(self::$piede);		
	}

	public function go() {

		require_once 'modificaPagamento.template.php';
		require_once 'ricercaScadenze.class.php';
		require_once 'utility.class.php';
		
		/**
		 * Crea una array dei dettagli per il controllo di quadratura
		 */
		$utility = Utility::getInstance();
		$this->prelevaDatiDettagliPagamento($utility);
		
		$result = $_SESSION["elencoDettagliPagamento"];
		
		$dettaglioPagamento = pg_fetch_all($result);
		$dett = "";
		
		foreach ($dettaglioPagamento as $row) {
			$dett = $dett . trim($row["cod_conto"]) . trim($row["cod_sottoconto"]) . "#" . trim($row["imp_registrazione"]) . "#" . trim($row["ind_dareavere"]) . ",";
		}
		$_SESSION['dettagliInseriti'] = $dett;
		
		$modificaPagamentoTemplate = ModificaPagamentoTemplate::getInstance();
		
		if ($modificaPagamentoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->aggiornaPagamento($utility)) {
		
				$_SESSION["messaggioModifica"] = "Pagamento salvato con successo";

				$fileClass = $_SESSION['referer_function_name'];
				
				if (strrpos($fileClass,"Scadenze") > 0) {
					$ricercaScadenze = RicercaScadenze::getInstance();
					$ricercaScadenze->go();
				}
			}
		}
		else {
				
			$this->preparaPagina($modificaPagamentoTemplate);
		
			include(self::$testata);
			$modificaPagamentoTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}
	
	public function prelevaDatiPagamento($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiRegistrazione($db, $utility, $_SESSION["idPagamento"]);
	
		if ($result) {
	
			$pagamento = pg_fetch_all($result);
			foreach ($pagamento as $row) {
	
				$_SESSION["descreg"] = $row["des_registrazione"];
				$_SESSION["datareg"] = $row["dat_registrazione"];
				$_SESSION["datareg_old"] = $row["dat_registrazione"];
				$_SESSION["numfatt"] = $row["num_fattura"];
				$_SESSION["numfatt_old"] = $row["num_fattura"];
				$_SESSION["codneg"] = $row["cod_negozio"];
				$_SESSION["causale"] = $row["cod_causale"];
				$_SESSION["fornitore"] = $row["id_fornitore"];
				$_SESSION["fornitore_old"] = $row["id_fornitore"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati pagamento : " . $_SESSION["idPagamento"] . " <<<<<<<<" );
		}
	}
	
	public function prelevaDatiDettagliPagamento($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiDettagliRegistrazione($db, $utility, $_SESSION["idPagamento"]);
	
		if ($result) {
			$_SESSION["elencoDettagliPagamento"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo dati pagamento (dettagli) : " . $_SESSION["idPagamento"] . " <<<<<<<<" );
		}
	}
	
	public function aggiornaPagamento($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
		
		/**
		 * Aggiornamento del pagamento
		 */		
		
		$descreg = str_replace("'", "''", $_SESSION["descreg"]);
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$stareg = $_SESSION["stareg"];
		$numfatt = ($_SESSION["numfatt"] != "") ? "'" . $_SESSION["numfatt"] . "'" : "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$fornitore = ($_SESSION["fornitore"] != "") ? $_SESSION["fornitore"] : "null" ;
		$staScadenza = "10";   // pagata
		
		if ($this->updateRegistrazione($db, $utility, $_SESSION["idPagamento"], $_SESSION["totaleDare"],
			$descreg, 'null', $datareg, $numfatt, $causale, $fornitore, 'null', $stareg,
			$codneg, $staScadenza)) {

			/**
			 * Se sono cambiati il codice fornitore o i numeri fattura, cambio lo stato alle fatture pagate: da "10" a "00"
			 * e riporto la registrazione originale a '00', valorizzo a null anche l'id_pagamento
			 */
			
			if ((trim($_SESSION["fornitore"]) != trim($_SESSION["fornitore_old"]) || (trim($_SESSION["numfatt"]) != trim($_SESSION["numfatt_old"])))) {
			
				$d = explode(",", $_SESSION["numfatt_old"]);
					
				foreach($d as $numeroFattura) {
					$numfatt = ($numeroFattura != "") ? "'" . $numeroFattura . "'" : "null" ;
					$this->cambiaStatoScadenzaFornitore($db, $utility, $_SESSION["fornitore_old"], $numfatt, '00', 'null');

					$result_idReg = $this->prelevaIdRegistrazioneOriginaleFornitore($db, $utility, $_SESSION["fornitore_old"], $numfatt);
						
					if ($result_idReg) {
							
						foreach(pg_fetch_all($result_idReg) as $row) {
							$idregistrazione = $row['id_registrazione'];		// l'id della fattura originale
						}
						$this->cambioStatoRegistrazione($db, $utility, $idregistrazione, '00');
					}						
				}
			}
				
			/**
			 * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
			 */
				
			$d = explode(",", $_SESSION["numfatt"]);
			
			foreach($d as $numeroFattura) {
				$numfatt = ($numeroFattura != "") ? "'" . $numeroFattura . "'" : "null" ;
				$this->cambiaStatoScadenzaFornitore($db, $utility, $_SESSION["fornitore"], $numfatt, '10', $_SESSION['idPagamento']);

				$result_idReg = $this->prelevaIdRegistrazioneOriginaleFornitore($db, $utility, $_SESSION["fornitore"], $numfatt);
				
				if ($result_idReg) {
						
					foreach(pg_fetch_all($result_idReg) as $row) {
						$idregistrazione = $row['id_registrazione'];		// l'id della fattura originale
					}
					$this->cambioStatoRegistrazione($db, $utility, $idregistrazione, '10');
				}				
			}				

			/**
			 * Rigenerazione dei saldi
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
			error_log("Errore aggiornamento pagamento, eseguito Rollback");
			return FALSE;
		}
	}
		
	public function preparaPagina($modificaPagamentoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaPagamentoTemplate->setAzione(self::$azioneModificaPagamento);
		$modificaPagamentoTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaPagamentoTemplate->setTitoloPagina("%ml.modificaPagamento%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per i combo --------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);

		/**
		 * Prepara il selectmenu delle scadenze aperte.
		 * Come per i conti, l'ajax non interviene se c'è un errore logico e la pagina viene ripresentata
		 */
		if (isset($_SESSION["fornitore"])) {
			$db = Database::getInstance();
			$utility = Utility::getInstance();
				
			$options = '';
				
			$result_scadenze_fornitore = $this->prelevaScadenzeFornitore($db, $utility, $_SESSION["fornitore"], $_SESSION["idRegistrazione"]);
				
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
			if (trim($numeroFattura) == trim($numFatt)) {
				$selected = "selected";
				break;
			}
		}
		return $selected;
	}	
}

?>