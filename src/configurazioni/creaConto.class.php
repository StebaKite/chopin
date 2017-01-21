<?php

require_once 'configurazioni.abstract.class.php';

class CreaConto extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneCreaConto = "../configurazioni/creaContoFacade.class.php?modo=go";

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

			self::$_instance = new CreaConto();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'creaConto.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaContoTemplate = CreaContoTemplate::getInstance();
		$this->preparaPagina($creaContoTemplate);

		$_SESSION["codconto"] = "";
		$_SESSION["desconto"] = "";
		
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaContoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {
	
		require_once 'creaConto.template.php';
		require_once 'utility.class.php';
	
		$utility = Utility::getInstance();

		$creaContoTemplate = CreaContoTemplate::getInstance();
		
		if ($creaContoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
				
			if ($this->creaConto($utility)) {

				session_unset();
				$_SESSION["messaggio"] = "Conto salvato con successo";
				
				$this->preparaPagina($creaContoTemplate);
				
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaContoTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {

				$this->preparaPagina($creaContoTemplate);
				
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaContoTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
				
				include(self::$piede);
			}
		}
		else {
				
			$this->preparaPagina($creaContoTemplate);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$creaContoTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function creaConto($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
		/**
		 * Crea il conto e tutti i suoi sottoconti
		*/
	
		$codconto = $_SESSION["codconto"];
		$desconto = $_SESSION["desconto"];
		$catconto = $_SESSION["catconto"];
		$tipconto = $_SESSION["tipconto"];
		$indpresenza = $_SESSION["indpresenza"];
		$indvisibilitasottoconti = $_SESSION["indvissottoconti"];
		$numrigabilancio = $_SESSION["numrigabilancio"];
			
		if ($this->inserisciConto($db, $utility, $codconto, $desconto, $catconto, $tipconto, $indpresenza, $indvisibilitasottoconti, $numrigabilancio)) {
	
			$d = explode(",", $_SESSION['sottocontiInseriti']);
	
			foreach($d as $ele) {
					
				$e = explode("#",$ele);
	
				$sottoconto = trim($e[0]);
				$dessottoconto = trim($e[1]);
				
				if (!$this->inserisciSottoconto($db, $utility, $codconto, $sottoconto, $dessottoconto)) {
					$db->rollbackTransaction();
					error_log("Errore inserimento sottoconto, eseguito Rollback");
					$_SESSION["messaggio"] = "Sottoconto già esistente, inserimento conto fallito";
					return FALSE;
				}
			}
	
			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento conto, eseguito Rollback");
		$_SESSION["messaggio"] = "Conto già esistente, inserimento fallito";
		return FALSE;
	}
	
	
	public function preparaPagina($creaContoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaContoTemplate->setAzione(self::$azioneCreaConto);
		$creaContoTemplate->setConfermaTip("%ml.confermaCreaConto%");
		$creaContoTemplate->setTitoloPagina("%ml.creaNuovoConto%");
	}
}		
		
?>