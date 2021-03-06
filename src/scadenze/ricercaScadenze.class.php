<?php

require_once 'scadenze.abstract.class.php';

class RicercaScadenze extends ScadenzeAbstract {

	private static $_instance = null;

	public static $azioneRicercaScadenze = "../scadenze/ricercaScadenzeFacade.class.php?modo=go";
	public static $queryRicercaScadenze = "/scadenze/ricercaScadenze.sql";
	
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

			self::$_instance = new RicercaScadenze();

		return self::$_instance;
	}

	public function start() {
		
		require_once 'ricercaScadenze.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$_SESSION["datascad_da"] = date("d/m/Y");
		$_SESSION["datascad_a"] = date("d/m/Y");
		$_SESSION["codneg_sel"] = "VIL";
		
		unset($_SESSION["scadenzeTrovate"]);
		unset($_SESSION['bottoneEstraiPdf']);
		unset($_SESSION['referer_function_name']);
		
		$ricercaScadenzeTemplate = RicercaScadenzeTemplate::getInstance();
		$this->preparaPagina($ricercaScadenzeTemplate);
		
		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$ricercaScadenzeTemplate->displayPagina();
		include(self::$piede);
		
	}

	public function go() {
	
		require_once 'ricercaScadenze.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$ricercaScadenzeTemplate = RicercaScadenzeTemplate::getInstance();
	
		if ($ricercaScadenzeTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($ricercaScadenzeTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$ricercaScadenzeTemplate->displayPagina();
	
				/**
				 * Gestione del messaggio proveniente da altre funzioni
				*/
				if (isset($_SESSION["messaggioCancellazione"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovate " . $_SESSION['numScadenzeTrovate'] . " scadenze";
					unset($_SESSION["messaggioCancellazione"]);
				}
				elseif (isset($_SESSION["messaggioModifica"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioModifica"] . "<br>" . "Trovate " . $_SESSION['numScadenzeTrovate'] . " scadenze";
					unset($_SESSION["messaggioModifica"]);
				}
				else {
					$_SESSION["messaggio"] = "Trovate " . $_SESSION['numScadenzeTrovate'] . " scadenze";
				}
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
	
				if ($_SESSION['numScadenzeTrovate'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}
	
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($ricercaScadenzeTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$ricercaScadenzeTemplate->displayPagina();
	
				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle scadenze" ;
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
	
			$this->preparaPagina($ricercaScadenzeTemplate);
	
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaScadenzeTemplate->displayPagina();
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';

		$filtro = "";

		if (($_SESSION['datascad_da'] != "") & ($_SESSION['datascad_a'] != "")) {
			$filtro = "AND scadenza.dat_scadenza between '" . $_SESSION['datascad_da'] . "' and '" . $_SESSION['datascad_a'] . "'" ;
		}
		
		if ($_SESSION['codneg_sel'] != "") {
			$filtro .= " AND scadenza.cod_negozio = '" . $_SESSION['codneg_sel'] . "'" ;
		}

		if ($_SESSION['statoscad_sel'] != "") {
			$filtro .= " AND scadenza.sta_scadenza = '" . $_SESSION['statoscad_sel'] . "'" ;
		}
		
		$replace = array(
				'%filtro_date%' => $filtro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenze;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['scadenzeTrovate'] = $result;
			$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";			
		}
		else {
			unset($_SESSION['scadenzeTrovate']);
			$_SESSION['numScadenzeTrovate'] = 0;
			unset($_SESSION['bottoneEstraiPdf']);			
		}
		return $result;
	}

	public function preparaPagina() {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaScadenze;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaScadenze%";
	}
}
		
?>