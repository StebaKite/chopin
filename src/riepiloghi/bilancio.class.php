<?php

require_once 'riepiloghi.abstract.class.php';

class Bilancio extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $azioneBilancioPeriodico = "../riepiloghi/bilancioFacade.class.php?modo=go";
	public static $azioneBilancioEsercizio = "../riepiloghi/bilancioEsercizioFacade.class.php?modo=go";
	
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

			self::$_instance = new Bilancio();

		return self::$_instance;
	}

	public function start() {

		require_once 'bilancio.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["codneg_sel"] = "VIL";
		$_SESSION["catconto"] = "Conto Economico";
		$_SESSION["soloContoEconomico"] = "N";
		
		unset($_SESSION["costiBilancio"]);
		unset($_SESSION["ricaviBilancio"]);
		unset($_SESSION["attivoBilancio"]);
		unset($_SESSION["passivoBilancio"]);
		unset($_SESSION['bottoneEstraiPdf']);		
		
		$bilancioTemplate = BilancioTemplate::getInstance();
		$this->preparaPagina($bilancioTemplate);
		
		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);
		
		$bilancioTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'bilancio.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$bilancioTemplate = BilancioTemplate::getInstance();
		
		if ($bilancioTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($bilancioTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$bilancioTemplate->displayPagina();

				if ($_SESSION["tipoBilancio"] == "Periodico") {
					$totVoci = $_SESSION['numCostiTrovati'] + $_SESSION['numRicaviTrovati'];
				}
				elseif ($_SESSION["tipoBilancio"] == "Esercizio") {
					$totVoci = $_SESSION['numAttivoTrovati'] + $_SESSION['numPassivoTrovati'];
				}				
				
				$_SESSION["messaggio"] = "Trovate " . $totVoci . " voci";
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);

				
				if ($totVoci > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}
		
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($bilancioTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$bilancioTemplate->displayPagina();
		
				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
		
			$this->preparaPagina($bilancioTemplate);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$bilancioTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';

		unset($_SESSION["costiBilancio"]);
		unset($_SESSION["ricaviBilancio"]);
		unset($_SESSION["attivoBilancio"]);
		unset($_SESSION["passivoBilancio"]);
		unset($_SESSION['bottoneEstraiPdf']);
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => ($_SESSION["codneg_sel"] == "") ? "'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'"
		);

		error_log("Estrazione bilancio esercizio : " . $_SESSION["datareg_da"] . " - " . $_SESSION["datareg_a"]);
		
		$db = Database::getInstance();

		$numCosti = $this->ricercaCosti($utility, $db, $replace);
		$numRicavi = $this->ricercaRicavi($utility, $db, $replace);			
			
			
		if ($_SESSION["soloContoEconomico"] == "N") {
			
			$numAttivo = $this->ricercaAttivo($utility, $db, $replace);
			$numPassivo = $this->ricercaPassivo($utility, $db, $replace);		
			$numCostiMct = $this->ricercaCostiMargineContribuzione($utility, $db, $replace);
			$numRicaviMct = $this->ricercaRicaviMargineContribuzione($utility, $db, $replace);
			$numCostiFissi = $this->ricercaCostiFissi($utility, $db, $replace);
								
			if (($numCosti > 0) or ($numRicavi > 0) or ($numAttivo > 0) or ($numPassivo > 0)) {
				$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
			}				
		}
		else {
			
			$numCostiMct = $this->ricercaCostiMargineContribuzione($utility, $db, $replace);
			$numRicaviMct = $this->ricercaRicaviMargineContribuzione($utility, $db, $replace);
			$numCostiFissi = $this->ricercaCostiFissi($utility, $db, $replace);
				
			if (($numCosti > 0) or ($numRicavi > 0)) {
				$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
			}
		}
		
		if ((is_numeric(trim($numCosti))) and 
			(is_numeric(trim($numRicavi))) and 
			(is_numeric(trim($numAttivo))) and 
			(is_numeric(trim($numPassivo))) and 
			(is_numeric(trim($numCostiMct))) and 
			(is_numeric(trim($numRicaviMct))) and 
			(is_numeric(trim($numCostiFissi)))) {
			return true;		
		}
		return false;
	}
	
	public function preparaPagina($bilancioTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["confermaTip"] = "%ml.confermaEstraiBilancio%";
		
		if ($_SESSION["tipoBilancio"] == "Periodico") {
			$_SESSION["azione"] = self::$azioneBilancioPeriodico;
			$_SESSION["titoloPagina"] = "%ml.bilancioPeriodico%";
		}
		elseif ($_SESSION["tipoBilancio"] == "Esercizio") {
			$_SESSION["azione"] = self::$azioneBilancioEsercizio;
			$_SESSION["titoloPagina"] = "%ml.bilancioEsercizio%";
		}
	}	
}

?>