<?php

require_once 'riepiloghi.abstract.class.php';

class AndamentoNegoziConfrontato extends RiepiloghiAbstract implements RiepiloghiBusinessInterface {

	private static $_instance = null;

	public static $azioneAndamentoNegoziConfrontato = "../riepiloghi/andamentoNegoziConfrontatoFacade.class.php?modo=go";

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

			self::$_instance = new AndamentoNegoziConfrontato();

			return self::$_instance;
	}


	public function start() {

		require_once 'andamentoNegoziConfrontato.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["datareg_da"] = "01/01/" . date("Y");
		$_SESSION["datareg_a"]  = "31/12/" . date("Y");
		$_SESSION["datareg_da_rif"] = "01/01/" . (date("Y") - 1);
		$_SESSION["datareg_a_rif"]  = "31/12/" . (date("Y") - 1);
		
		unset($_SESSION["codneg_sel"]);
		unset($_SESSION["codneg_sel_rif"]);
		unset($_SESSION["elencoVociAndamentoCostiNegozio"]);
		unset($_SESSION["elencoVociAndamentoRicaviNegozio"]);
		unset($_SESSION["elencoVociAndamentoCostiNegozioRiferimento"]);
		unset($_SESSION["elencoVociAndamentoRicaviNegozioRiferimento"]);
		unset($_SESSION["elencoVociDeltaCostiNegozio"]);
		unset($_SESSION["elencoVociDeltaRicaviNegozio"]);
		unset($_SESSION['bottoneEstraiPdf']);

		$andamentoNegoziConfrontatoTemplate = AndamentoNegoziConfrontatoTemplate::getInstance();
		$this->preparaPagina($andamentoNegoziConfrontatoTemplate);

		// compongo la pagina

		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$andamentoNegoziConfrontatoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'andamentoNegoziConfrontato.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$andamentoNegoziConfrontatoTemplate = AndamentoNegoziConfrontatoTemplate::getInstance();

		if ($andamentoNegoziConfrontatoTemplate->controlliLogici()) {

			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($andamentoNegoziConfrontatoTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$andamentoNegoziConfrontatoTemplate->displayPagina();

				$numCosti = $_SESSION['numCostiTrovati'];
				$numRicavi = $_SESSION['numRicaviTrovati'];
				$numCostiRiferimento = $_SESSION['numCostiTrovatiRiferimento'];
				$numRicaviRiferimento = $_SESSION['numRicaviTrovatiRiferimento'];
				
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
					
				$this->preparaPagina($andamentoNegoziConfrontatoTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$andamentoNegoziConfrontatoTemplate->displayPagina();

				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
			$this->preparaPagina($andamentoNegoziConfrontatoTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$andamentoNegoziConfrontatoTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	private function ricercaDati($utility) {

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

		/**
		 * Estrazione Costi e Ricavi del periodo corrente
		 */
		$numCostiCor = $this->ricercaVociAndamentoCostiNegozio($utility, $db, $replace);
		$numRicaviCor = $this->ricercaVociAndamentoRicaviNegozio($utility, $db, $replace);
				
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da_rif"],
				'%datareg_a%' => $_SESSION["datareg_a_rif"],
				'%codnegozio%' => $codnegozio
		);

		/**
		 * Estrazione Costi e Ricavi del periodo di riferimento
		 */
		$numCostiRif  = $this->ricercaVociAndamentoCostiNegozioRiferimento($utility, $db, $replace);
		$numRicaviRif = $this->ricercaVociAndamentoRicaviNegozioRiferimento($utility, $db, $replace);
		
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

	private function preparaPagina($bilancioTemplate) {

		require_once 'utility.class.php';

		$_SESSION["confermaTip"] = "%ml.confermaEstraiRiepilogo%";
		$_SESSION["azione"] = self::$azioneAndamentoNegoziConfrontato;
		$_SESSION["titoloPagina"] = "%ml.confrontoNegozi%";
	}
}

?>
	