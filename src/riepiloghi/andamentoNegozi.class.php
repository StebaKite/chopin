<?php

require_once 'riepiloghi.abstract.class.php';

class AndamentoNegozi extends RiepiloghiAbstract {

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

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$_SESSION["datareg_da"] = "01/01/" . date("Y");
		$_SESSION["datareg_a"]  = "31/12/" . date("Y");
		unset($_SESSION["elencoVociAndamentoNegozio"]);
		unset($_SESSION['bottoneEstraiPdf']);
		
		$andamentoNegoziTemplate = AndamentoNegoziTemplate::getInstance();
		$this->preparaPagina($andamentoNegoziTemplate);

		// compongo la pagina
		
		include($testata);
		$andamentoNegoziTemplate->displayPagina();
		include($piede);
	}

	public function go() {

		require_once 'andamentoNegozi.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$andamentoNegoziTemplate = AndamentoNegoziTemplate::getInstance();

		if ($andamentoNegoziTemplate->controlliLogici()) {
		
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($andamentoNegoziTemplate);
					
				include(self::$testata);
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
					
				include(self::$testata);
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
		
			include(self::$testata);
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
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => $_SESSION["codneg_sel"]
		);
	
		$db = Database::getInstance();
		
		if ($this->ricercaVociAndamentoCostiNegozio($utility, $db, $replace)) {
		
			$this->ricercaVociAndamentoRicaviNegozio($utility, $db, $replace);
			$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
			return TRUE;
		}
		return FALSE;		
	}

	public function preparaPagina($bilancioTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["confermaTip"] = "%ml.confermaEstraiRiepilogo%";
		$_SESSION["azione"] = self::$azioneAndamentoNegozi;
		$_SESSION["titoloPagina"] = "%ml.andamentoNegozi%";
	}
}

?>
	