<?php

require_once 'riepiloghi.abstract.class.php';

class RiepilogoNegozi extends RiepiloghiAbstract {

	private static $_instance = null;
	
	public static $azioneRiepilogoNegozi = "../riepiloghi/riepilogoNegoziFacade.class.php?modo=go";

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
	
			self::$_instance = new RiepilogoNegozi();
	
			return self::$_instance;
	}


	public function start() {
	
		require_once 'riepilogoNegozi.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
	
		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["catconto"] = "Conto Economico";
		$_SESSION["soloContoEconomico"] = "N";
	
		unset($_SESSION["costiComparati"]);
		unset($_SESSION["ricaviComparati"]);
		unset($_SESSION["attivoComparati"]);
		unset($_SESSION["passivoComparati"]);
		unset($_SESSION['bottoneEstraiPdf']);
	
		$riepilogoNegoziTemplate = RiepilogoNegoziTemplate::getInstance();
		$this->preparaPagina($riepilogoNegoziTemplate);
	
		// compone la pagina
		include($testata);
		$riepilogoNegoziTemplate->displayPagina();
		include($piede);
	}

	public function go() {
	
		require_once 'riepilogoNegozi.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
	
		$riepilogoNegoziTemplate = RiepilogoNegoziTemplate::getInstance();

		if ($riepilogoNegoziTemplate->controlliLogici()) {
		
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($riepilogoNegoziTemplate);
					
				include(self::$testata);
				$riepilogoNegoziTemplate->displayPagina();
		
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
					
				$this->preparaPagina($riepilogoNegoziTemplate);
					
				include(self::$testata);
				$riepilogoNegoziTemplate->displayPagina();
		
				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
		
			$this->preparaPagina($riepilogoNegoziTemplate);
		
			include(self::$testata);
			$riepilogoNegoziTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		unset($_SESSION["costiComparati"]);
		unset($_SESSION["ricaviComparati"]);
		unset($_SESSION["attivoComparati"]);
		unset($_SESSION["passivoComparati"]);
		unset($_SESSION['bottoneEstraiPdf']);
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"]
		);
	
		error_log("Estrazione bilancio esercizio : " . $_SESSION["datareg_da"] . " - " . $_SESSION["datareg_a"]);
	
		$db = Database::getInstance();
	
		if ($this->ricercaCostiComparati($utility, $db, $replace)) {
			if ($this->ricercaRicaviComparati($utility, $db, $replace)) {
				if ($_SESSION["soloContoEconomico"] == "N") {
						
					if ($this->ricercaAttivoComparati($utility, $db, $replace)) {
						if ($this->ricercaPassivoComparati($utility, $db, $replace)) {

							$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
							return TRUE;
								
							if ($this->ricercaCostiMargineContribuzione($utility, $db, $replace)) {
								if ($this->ricercaRicaviMargineContribuzione($utility, $db, $replace)) {
									if ($this->ricercaCostiFissi($utility, $db, $replace)) {
										$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
										return TRUE;
									}
								}
							}
						}
					}	
				}
				else {
					if ($this->ricercaCostiMargineContribuzione($utility, $db, $replace)) {
						if ($this->ricercaRicaviMargineContribuzione($utility, $db, $replace)) {
							if ($this->ricercaCostiFissi($utility, $db, $replace)) {
								$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
								return TRUE;
							}
						}
					}
				}
			}
		}
		return FALSE;
	}

	public function preparaPagina($bilancioTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["confermaTip"] = "%ml.confermaEstraiRiepilogo%";	
		$_SESSION["azione"] = self::$azioneRiepilogoNegozi;
		$_SESSION["titoloPagina"] = "%ml.riepilogoNegozi%";
	}
}
