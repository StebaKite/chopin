<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.interface.php';

class AndamentoNegozi extends RiepiloghiAbstract implements Riepiloghi {

	private static $_instance = null;

	public static $azioneAndamentoNegozi = "../riepiloghi/andamentoNegoziFacade.class.php?modo=go";

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

			self::$_instance = new AndamentoNegozi();

			return self::$_instance;
	}


	public function start() {

		require_once 'andamentoNegozi.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["datareg_da"] = "01/01/" . date("Y");
		$_SESSION["datareg_a"]  = "31/12/" . date("Y");
		
		unset($_SESSION["elencoVociAndamentoCostiNegozio"]);
		unset($_SESSION["elencoVociAndamentoRicaviNegozio"]);
		unset($_SESSION['bottoneEstraiPdf']);
		
		$andamentoNegoziTemplate = AndamentoNegoziTemplate::getInstance();
		$this->preparaPagina($andamentoNegoziTemplate);

		// compongo la pagina
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);
		
		$andamentoNegoziTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'andamentoNegozi.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$andamentoNegoziTemplate = AndamentoNegoziTemplate::getInstance();

		if ($andamentoNegoziTemplate->controlliLogici()) {
		
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($andamentoNegoziTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$andamentoNegoziTemplate->displayPagina();
		
				$numCosti = $_SESSION['numCostiTrovati'];
				$numRicavi = $_SESSION['numRicaviTrovati'];
				
				$_SESSION["messaggio"] = "Trovate " . $numCosti . " voci di costo e " . $numRicavi . " voci di ricavo";
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
		
				if (($numCosti > 0) or ($numRicavi > 0)) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}
		
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($andamentoNegoziTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$andamentoNegoziTemplate->displayPagina();
		
				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
			$this->preparaPagina($andamentoNegoziTemplate);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$andamentoNegoziTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		unset($_SESSION["totaliProgressivi"]);
	
		$codnegozio = "";
		$codnegozio = ($_SESSION["codneg_sel"] == "") ?"'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'";
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => $codnegozio
		);
	
		$db = Database::getInstance();
		
		$numCostiCor = $this->ricercaVociAndamentoCostiNegozio($utility, $db, $replace);
		$numRicaviCor = $this->ricercaVociAndamentoRicaviNegozio($utility, $db, $replace);
		
		/**
		 * Un contatore che contiene "" indica un accesso a db fallito
		 */
		
		if (($numCostiCor > 0) or ($numRicaviCor > 0)) {
			$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
		}
		else {
			unset($_SESSION['bottoneEstraiPdf']);
		}
		return true;		
	}

	public function preparaPagina($bilancioTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["confermaTip"] = "%ml.confermaEstraiRiepilogo%";
		$_SESSION["azione"] = self::$azioneAndamentoNegozi;
		$_SESSION["titoloPagina"] = "%ml.andamentoNegozi%";
	}
}

?>
	