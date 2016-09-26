<?php

require_once 'configurazioni.abstract.class.php';

class CreaCausale extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneCreaCausale = "../configurazioni/creaCausaleFacade.class.php?modo=go";

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

			self::$_instance = new CreaCausale();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'creaCausale.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaCausaleTemplate = CreaCausaleTemplate::getInstance();
		$this->preparaPagina($creaCausaleTemplate);
		
		$_SESSION["codcausale"] = "";
		$_SESSION["descausale"] = "";
		$_SESSION["catcausale"] = "";
		
		// Compone la pagina
		$replace = array('%amb%' => $_SESSION["ambiente"]);
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaCausaleTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {
	
		require_once 'creaCausale.template.php';
		require_once 'utility.class.php';
	
		$utility = Utility::getInstance();
	
		$creaCausaleTemplate = CreaCausaleTemplate::getInstance();
	
		if ($creaCausaleTemplate->controlliLogici()) {
	
			// Aggiornamento del DB ------------------------------
	
			if ($this->creaCausale($utility)) {
	
				session_unset();
				$_SESSION["messaggio"] = "Causale salvata con successo";
	
				$this->preparaPagina($creaCausaleTemplate);
	
				$replace = array('%amb%' => $_SESSION["ambiente"]);
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaCausaleTemplate->displayPagina();
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
	
				$this->preparaPagina($creaCausaleTemplate);
	
				$replace = array('%amb%' => $_SESSION["ambiente"]);
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaCausaleTemplate->displayPagina();
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
	
				include(self::$piede);
			}
		}
		else {
	
			$this->preparaPagina($creaCausaleTemplate);
	
			$replace = array('%amb%' => $_SESSION["ambiente"]);
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$creaCausaleTemplate->displayPagina();
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
	}

	public function creaCausale($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
	
		$codcausale = $_SESSION["codcausale"];
		$descausale = $_SESSION["descausale"];
		$catcausale = $_SESSION["catcausale"];
			
		if ($this->inserisciCausale($db, $utility, $codcausale, $descausale, $catcausale)) {
	
			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento causale, eseguito Rollback");
		$_SESSION["messaggio"] = "Causale già esistente, inserimento fallito";
		return FALSE;
	}
	
	
	public function preparaPagina($creaCausaleTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaCausaleTemplate->setAzione(self::$azioneCreaCausale);
		$creaCausaleTemplate->setConfermaTip("%ml.confermaCreaCausale%");
		$creaCausaleTemplate->setTitoloPagina("%ml.creaNuovaCausale%");
	}
}
		
?>