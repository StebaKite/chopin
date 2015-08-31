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
		
		$creaFornitoreTemplate = CreaFornitoreTemplate::getInstance();
		$this->preparaPagina($creaFornitoreTemplate);
		
		$_SESSION["codfornitore"] = "";
		$_SESSION["desfornitore"] = "";
		$_SESSION["indfornitore"] = "";
		$_SESSION["cittafornitore"] = "";
		$_SESSION["capfornitore"] = "";
		
		// Compone la pagina
		include(self::$testata);
		$creaFornitoreTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
	
		require_once 'creaFornitore.template.php';
		require_once 'utility.class.php';
	
		$utility = Utility::getInstance();

		$creaFornitoreTemplate = CreaFornitoreTemplate::getInstance();
		
		if ($creaFornitoreTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->creaFornitore($utility)) {
		
				session_unset();
				$_SESSION["messaggio"] = "Fornitore salvato con successo";
		
				$this->preparaPagina($creaFornitoreTemplate);
		
				include(self::$testata);
				$creaFornitoreTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
		
				$this->preparaPagina($creaFornitoreTemplate);
		
				include(self::$testata);
				$creaFornitoreTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
		
				include(self::$piede);
			}
		}
		else {
		
			$this->preparaPagina($creaFornitoreTemplate);
		
			include(self::$testata);
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
		
		if ($this->inserisciFornitore($db, $utility, $codfornitore, $desfornitore, $indfornitore, $cittafornitore, $capfornitore, $tipoaddebito)) {
	
			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento fornitore, eseguito Rollback");
		$_SESSION["messaggio"] = "Fornitore già esistente, inserimento fallito";
		return FALSE;
	}
	
	public function preparaPagina($creaCausaleTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaCausaleTemplate->setAzione(self::$azioneCreaFornitore);
		$creaCausaleTemplate->setConfermaTip("%ml.confermaCreaFornitore%");
		$creaCausaleTemplate->setTitoloPagina("%ml.creaNuovoFornitore%");
	}
}

?>