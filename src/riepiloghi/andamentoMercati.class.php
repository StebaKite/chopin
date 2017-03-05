<?php

require_once 'riepiloghi.abstract.class.php';

class AndamentoMercati extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $azioneAndamentoMercati = "../riepiloghi/andamentoMercatiFacade.class.php?modo=go";

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

			self::$_instance = new AndamentoMercati();

			return self::$_instance;
	}


	public function start() {

		require_once 'andamentoMercati.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["datareg_da"] = "01/01/" . date("Y");
		$_SESSION["datareg_a"]  = "31/12/" . date("Y");

		$negozi = explode(",", $array["negozi"]);
		foreach($negozi as $negozio) {
			unset($_SESSION["elencoVociAndamentoRicaviNegozio_" . $negozio] );			
		}
		unset($_SESSION['bottoneEstraiPdf']);

		$andamentoMercatiTemplate = AndamentoMercatiTemplate::getInstance();
		$this->preparaPagina($andamentoMercatiTemplate);

		// compongo la pagina

		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$andamentoMercatiTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'andamentoMercati.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$andamentoMercatiTemplate = AndamentoMercatiTemplate::getInstance();

		if ($andamentoMercatiTemplate->controlliLogici()) {

			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($andamentoMercatiTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$andamentoMercatiTemplate->displayPagina();

				$negozi = explode(",", $array["negozi"]);
				foreach($negozi as $negozio) {
					$numRicavi += $_SESSION["numRicaviTrovati_". $negozio];
				}

				$_SESSION["messaggio"] = "Trovate " . $numRicavi . " voci di ricavo";
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);

				if ($numRicavi > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}

				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($andamentoMercatiTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$andamentoMercatiTemplate->displayPagina();

				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
			$this->preparaPagina($andamentoMercatiTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$andamentoMercatiTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {

		require_once 'database.class.php';

		$array = $utility->getConfig();
		$db = Database::getInstance();
		
		unset($_SESSION["totaliProgressivi"]);	
		
		$negozi = explode(",", $array["negozi"]);
		$numRicaviTotali = 0;
		
		foreach($negozi as $negozio) {
			
			$replace = array(
					'%datareg_da%' => $_SESSION["datareg_da"],
					'%datareg_a%' => $_SESSION["datareg_a"],
					'%codnegozio%' => $negozio
			);
				
			$numRicavi = $this->ricercaVociAndamentoRicaviMercato($utility, $db, $replace, $negozio);			
			
			/**
			 * Un contatore che contiene "" indica un accesso a db fallito
			 */

			if ($numRicavi === "") {
				return false;
			}
			else {
				$numRicaviTotali += $numRicavi;
				if ($numRicaviTotali > 0) {
					$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
				}
				else {
					unset($_SESSION['bottoneEstraiPdf']);
				}
			}
		}
		return true;
	}

	public function preparaPagina($bilancioTemplate) {

		require_once 'utility.class.php';

		$_SESSION["confermaTip"] = "%ml.confermaEstraiRiepilogo%";
		$_SESSION["azione"] = self::$azioneAndamentoMercati;
		$_SESSION["titoloPagina"] = "%ml.andamentoMercati%";
	}
}

?>
	