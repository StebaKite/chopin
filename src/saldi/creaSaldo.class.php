<?php

require_once 'saldi.abstract.class.php';

class CreaSaldo extends SaldiAbstract {

	private static $_instance = null;

	public static $azioneCreaSaldo = "../saldi/creaSaldoFacade.class.php?modo=go";

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

			self::$_instance = new CreaSaldo();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'creaSaldo.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaSaldoTemplate = CreaSaldoTemplate::getInstance();
		$this->preparaPagina($creaSaldoTemplate);

		// Data del giorno preimpostata solo in entrata -------------------------

		$_SESSION["codneg"] = "VIL";

		// Compone la pagina
		$replace = array('%amb%' => $_SESSION["ambiente"]);
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaSaldoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'creaSaldo.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();

		$creaSaldoTemplate = CreaSaldoTemplate::getInstance();

		if ($creaSaldoTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------

			if ($this->gestisciSaldo($utility)) {

				$_SESSION["messaggio"] = "Saldo salvato con successo";
				unset($_SESSION["codconto"]);
				unset($_SESSION["impsaldo"]);

				$this->preparaPagina($creaSaldoTemplate);

				$replace = array('%amb%' => $_SESSION["ambiente"]);
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaSaldoTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {

			$this->preparaPagina($creaSaldoTemplate);

			$replace = array('%amb%' => $_SESSION["ambiente"]);
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$creaSaldoTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	public function gestisciSaldo($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		$db->beginTransaction();

		$codneg = $_SESSION["codneg"];

		$d = explode("-", $_SESSION["codconto"]);
		$codconto = $d[0];		
		$codsottoconto = $d[1];
		
		$datsaldo = $_SESSION["datsaldo"];
		$dessaldo = str_replace("'","''",$_SESSION["dessaldo"]);
		$impsaldo = str_replace(",",".",$_SESSION["impsaldo"]);
		$dareavere = $_SESSION["dareavere"];
			
		if ($this->inserisciSaldo($db, $utility, $codneg, $codconto, $codsottoconto, $datsaldo, $dessaldo, $impsaldo, $dareavere)) {
		
			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento saldo, eseguito Rollback");
		return FALSE;				
	}

	public function preparaPagina($creaSaldoTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaSaldoTemplate->setAzione(self::$azioneCreaSaldo);
		$creaSaldoTemplate->setConfermaTip("%ml.confermaCreaSaldo%");
		$creaSaldoTemplate->setTitoloPagina("%ml.creaNuovoSaldo%");

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		*/
		$_SESSION['elenco_conti'] = $this->caricaTuttiConti($utility, $db);

	}
}

?>