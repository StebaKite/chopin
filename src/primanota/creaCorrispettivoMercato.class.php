<?php

require_once 'primanota.abstract.class.php';

class CreaCorrispettivoMercato extends primanotaAbstract {

	private static $_instance = null;
	private static $contoErario = null;
	private static $contoCorrispettivo = null;

	public static $azioneCreaCorrispettivoMercato = "../primanota/creaCorrispettivoMercatoFacade.class.php?modo=go";

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];

		self::$contoErario = $array['contoErarioMercati'];
		self::$contoCorrispettivo = $array['contoCorrispettivoMercati'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CreaCorrispettivoMercato();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'creaCorrispettivoMercato.template.php';

		$creaCorrispettivoMercatoTemplate = CreaCorrispettivoMercatoTemplate::getInstance();
		$this->preparaPagina($creaCorrispettivoMercatoTemplate);

		// Data del giorno preimpostata solo in entrata -------------------------

		$_SESSION["datareg"] = date("d/m/Y");
		$_SESSION["codneg"] = "VIL";

		// Compone la pagina
		include(self::$testata);
		$creaCorrispettivoMercatoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'creaCorrispettivoMercato.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();

		$creaCorrispettivoMercatoTemplate = CreaCorrispettivoMercatoTemplate::getInstance();

		if ($creaCorrispettivoMercatoTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------

			if ($this->creaCorrispettivoMercato($utility)) {

				session_unset();
				$_SESSION["messaggio"] = "Corrispettivo salvato con successo";
				$_SESSION["datareg"] = date("d/m/Y");
				$_SESSION["codneg"] = "VIL";

				$this->preparaPagina($creaCorrispettivoMercatoTemplate);

				include(self::$testata);
				$creaCorrispettivoMercatoTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {

			$this->preparaPagina($creaCorrispettivoMercatoTemplate);

			include(self::$testata);
			$creaCorrispettivoMercatoTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	public function creaCorrispettivoMercato($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		$db->beginTransaction();

		/**
		 * Crea la registrazione e tutti i suoi dettagli
		*/

		$descreg = $_SESSION["descreg"];
		$datascad = "null" ;
		$datareg = ($_SESSION["datareg"] != "") ? "'" . $_SESSION["datareg"] . "'" : "null" ;
		$numfatt = "null" ;
		$codneg = ($_SESSION["codneg"] != "") ? "'" . $_SESSION["codneg"] . "'" : "null" ;
		$causale = $_SESSION["causale"];
		$stareg = $_SESSION["stareg"];
		$fornitore = "null" ;
		$cliente = "null" ;

		if ($this->inserisciRegistrazione($db, $utility, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $codneg, $stareg)) {

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
				$this->rigenerazioneSaldi($db, $utility, strtotime(str_replace('/', '-', str_replace("'", "", $datareg))));
			}
				
			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento registrazione, eseguito Rollback");
		return FALSE;
	}

	public function preparaPagina($creaCorrispettivoMercatoTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaCorrispettivoMercatoTemplate->setAzione(self::$azioneCreaCorrispettivoMercato);
		$creaCorrispettivoMercatoTemplate->setConfermaTip("%ml.confermaCreaCorrispettivoMercato%");
		$creaCorrispettivoMercatoTemplate->setTitoloPagina("%ml.creaNuovoCorrispettivoMercato%");

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		// Prelievo dei dati per popolare i combo -------------------------------------------------------------

		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);

		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		*/
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);

		// Conti per erario e cassa/banca da passare alla script in pagina ---------

		$_SESSION['conto_erario_negozi'] = self::$contoErario;
		$_SESSION['conto_corrispettivo_negozi'] = self::$contoCorrispettivo;
	}
}

?>