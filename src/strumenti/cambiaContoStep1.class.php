<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep1 extends StrumentiAbstract {

	private static $_instance = null;

	public static $azioneRicercaRegistrazioniConto = "../strumenti/cambiaContoStep1Facade.class.php?modo=go";
		
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
	
			self::$_instance = new CambiaContoStep1();
	
		return self::$_instance;
	}
	
	public function start() {

		require_once 'cambiaContoStep1.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["datareg_da"] = (isset($_SESSION["datareg_da"])) ? $_SESSION["datareg_da"] : date("d/m/Y");
		$_SESSION["datareg_a"] = (isset($_SESSION["datareg_a"])) ? $_SESSION["datareg_a"] : date("d/m/Y");
		$_SESSION["codneg_sel"] = (isset($_SESSION["codneg_sel"])) ? $_SESSION["codneg_sel"] : "VIL";
		
		unset($_SESSION["registrazioniTrovate"]);
		
		$cambiaContoStep1Template = CambiaContoStep1Template::getInstance();
		$this->preparaPagina($cambiaContoStep1Template);
		
		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$cambiaContoStep1Template->displayPagina();

		/**
		 * Gestione del messaggio proveniente da altre funzioni
		 */
		if (isset($_SESSION["messaggioCambioConto"])) {
			self::$replace = array('%messaggio%' => $_SESSION["messaggioCambioConto"]);
			unset($_SESSION["messaggioCambioConto"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			echo $utility->tailTemplate($template);
		}		
		include(self::$piede);
	}

	public function go() {

		require_once 'cambiaContoStep1.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$cambiaContoStep1Template = CambiaContoStep1Template::getInstance();

		if ($cambiaContoStep1Template->controlliLogici()) {
			
			if ($this->ricercaDati($utility)) {
			
				$this->preparaPagina($cambiaContoStep1Template);
			
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$cambiaContoStep1Template->displayPagina();
				
				$_SESSION["messaggio"] = "Trovate " . $_SESSION['numRegTrovate'] . " registrazioni";				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				
				if ($_SESSION['numRegTrovate'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);						
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);						
				}
				
				echo $utility->tailTemplate($template);
			
				include(self::$piede);
			}
			else {
			
				$this->preparaPagina($cambiaContoStep1Template);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$cambiaContoStep1Template->displayPagina();

				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
				
			$this->preparaPagina($cambiaContoStep1Template);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$cambiaContoStep1Template->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}
	
	public function ricercaDati($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		$db->beginTransaction();
		
		$result = $this->caricaRegistrazioniConto($utility, $db);
		
		if (pg_num_rows($result) > 0) {
			$_SESSION['registrazioniTrovate'] = $result;
		}
		else {
			unset($_SESSION['registrazioniTrovate']);
			$_SESSION['numRegTrovate'] = 0;
		}
		$db->commitTransaction();		
		return $result;
	}
	
	public function preparaPagina($ricercaRegistrazioneTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneRicercaRegistrazioniConto;
		$_SESSION["confermaTip"] = "%ml.confermaRicercaRegistrazione%";
		$_SESSION["titoloPagina"] = "%ml.cambioContoStep1%";
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		// Prelievo dei dati per popolare i combo -------------------------------------------------------------
		
		$_SESSION['elenco_conti'] = $this->caricaTuttiConti($utility, $db);
	}
}
	
?>