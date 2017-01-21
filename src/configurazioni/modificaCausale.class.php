<?php

require_once 'configurazioni.abstract.class.php';

class ModificaCausale extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneModificaCausale = "../configurazioni/modificaCausaleFacade.class.php?modo=go";

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

			self::$_instance = new ModificaCausale();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaCausale.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		$this->prelevaCausale($utility);
		
		$modificaCausaleTemplate = ModificaCausaleTemplate::getInstance();
		$this->preparaPagina($modificaCausaleTemplate);
			
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);
		
		$modificaCausaleTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {
	
		require_once 'modificaCausale.template.php';
		require_once 'utility.class.php';
	
		$utility = Utility::getInstance();	
		$modificaCausaleTemplate = ModificaCausaleTemplate::getInstance();
	
		if ($modificaCausaleTemplate->controlliLogici()) {
	
			// Aggiornamento del DB ------------------------------
	
			if ($this->aggiornaCausale($utility)) {
	
				$_SESSION["messaggio"] = "Causale salvata con successo";
	
				$this->preparaPagina($modificaCausaleTemplate);
	
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);
				$modificaCausaleTemplate->displayPagina();
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($modificaCausaleTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);
				
				$modificaCausaleTemplate->displayPagina();
					
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
	}
	
	public function prelevaCausale($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiCausale($db, $utility, $_SESSION["codcausale"]);
	
		if ($result) {
	
			$conto = pg_fetch_all($result);
			foreach ($conto as $row) {
	
				$_SESSION["descausale"] = $row["des_causale"];
				$_SESSION["catcausale"] = $row["cat_causale"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati causale : " . $_SESSION["codcausale"] . " <<<<<<<<" );
		}
	}

	public function aggiornaCausale($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
		
		if ($this->updateCausale($db, $utility, $_SESSION["codcausale"], $_SESSION["descausale"], $_SESSION["catcausale"])) {
	
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento causale, eseguito Rollback");
			return FALSE;
		}
	}
	
	public function preparaPagina($modificaCausaleTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaCausaleTemplate->setAzione(self::$azioneModificaCausale);
		$modificaCausaleTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaCausaleTemplate->setTitoloPagina("%ml.modificaCausale%");
	}
}

?>