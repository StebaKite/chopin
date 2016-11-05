<?php

require_once 'anagrafica.abstract.class.php';

class CreaFornitore extends AnagraficaAbstract {

	public static $_instance = null;

	public static $azioneCreaFornitore = "../anagrafica/creaFornitoreFacade.class.php?modo=go";

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

			self::$_instance = new CreaFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'creaFornitore.template.php';
		require_once 'database.class.php';
		require_once 'utility.class.php';
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$creaFornitoreTemplate = CreaFornitoreTemplate::getInstance();
		$this->preparaPagina($creaFornitoreTemplate);
		
		$_SESSION["codfornitore"] = $this->prelevaUltimoCodiceFornitore($utility, $db) + 1;
		unset($_SESSION["desfornitore"]);
		unset($_SESSION["indfornitore"]);
		unset($_SESSION["cittafornitore"]);
		unset($_SESSION["capfornitore"]);
		
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaFornitoreTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
	
		require_once 'creaFornitore.template.php';
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$utility = Utility::getInstance();

		$creaFornitoreTemplate = CreaFornitoreTemplate::getInstance();
		
		if ($creaFornitoreTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->creaFornitore($utility)) {

				$db = Database::getInstance();
				$utility = Utility::getInstance();
				
				$_SESSION["codfornitore"] = $this->prelevaUltimoCodiceFornitore($utility, $db) + 1;
				unset($_SESSION["desfornitore"]);
				unset($_SESSION["indfornitore"]);
				unset($_SESSION["cittafornitore"]);
				unset($_SESSION["capfornitore"]);
				
				$_SESSION["messaggio"] = "Fornitore salvato con successo";
		
				$this->preparaPagina($creaFornitoreTemplate);
		
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);
				
				$creaFornitoreTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
		
				$this->preparaPagina($creaFornitoreTemplate);
		
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaFornitoreTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
		
				include(self::$piede);
			}
		}
		else {
		
			$this->preparaPagina($creaFornitoreTemplate);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$creaFornitoreTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function creaFornitore($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
	
		$codfornitore = $_SESSION["codfornitore"];
		$desfornitore = $_SESSION["desfornitore"];
		$indfornitore = $_SESSION["indfornitore"];
		$cittafornitore = $_SESSION["cittafornitore"];
		$capfornitore = $_SESSION["capfornitore"];
		$tipoaddebito = $_SESSION["tipoaddebito"];
		$numggscadenzafattura = $_SESSION["numggscadenzafattura"];
		
		if ($this->inserisciFornitore($db, $utility, $codfornitore, $desfornitore, $indfornitore, $cittafornitore, $capfornitore, $tipoaddebito, $numggscadenzafattura)) {
	
			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento fornitore, eseguito Rollback");
		$_SESSION["messaggio"] = "Fornitore già esistente, inserimento fallito";
		return FALSE;
	}
	
	public function preparaPagina($creaFornitoreTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaFornitoreTemplate->setAzione(self::$azioneCreaFornitore);
		$creaFornitoreTemplate->setConfermaTip("%ml.confermaCreaFornitore%");
		$creaFornitoreTemplate->setTitoloPagina("%ml.creaNuovoFornitore%");
	}
}

?>