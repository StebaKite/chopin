<?php

require_once 'riepiloghi.abstract.class.php';

class Bilancio extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $azioneBilancioPeriodico = "../riepiloghi/bilancioFacade.class.php?modo=go";
	public static $azioneBilancioEsercizio = "../riepiloghi/bilancioEsercizioFacade.class.php?modo=go";
	public static $queryCosti = "/riepiloghi/costi.sql";
	public static $queryRicavi = "/riepiloghi/ricavi.sql";
	public static $queryAttivo = "/riepiloghi/attivo.sql";
	public static $queryPassivo = "/riepiloghi/passivo.sql";
	
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
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["codneg_sel"] = "VIL";
		$_SESSION["catconto"] = "Conto Economico";
		
		unset($_SESSION["costiBilancio"]);
		unset($_SESSION["ricaviBilancio"]);
		unset($_SESSION["attivoBilancio"]);
		unset($_SESSION["passivoBilancio"]);
		unset($_SESSION['bottoneEstraiPdf']);
		
		$bilancioTemplate = BilancioTemplate::getInstance();
		$this->preparaPagina($bilancioTemplate);
		
		// compone la pagina
		include($testata);
		$bilancioTemplate->displayPagina();
		include($piede);
	}

	public function go() {

		require_once 'bilancio.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$bilancioTemplate = BilancioTemplate::getInstance();
		
		if ($bilancioTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($bilancioTemplate);
					
				include(self::$testata);
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
					
				include(self::$testata);
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
		
			include(self::$testata);
			$bilancioTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => $_SESSION["codneg_sel"]
		);
		
		$db = Database::getInstance();
		
		if ($_SESSION["tipoBilancio"] == "Periodico") {
			if ($this->ricercaCosti($utility, $db, $replace)) {
				if ($this->ricercaRicavi($utility, $db, $replace)) {
					$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
					return TRUE;
				}
			}				
		}
		elseif ($_SESSION["tipoBilancio"] == "Esercizio") {
			if ($this->ricercaAttivo($utility, $db, $replace)) {
				if ($this->ricercaPassivo($utility, $db, $replace)) {
					$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
					return TRUE;
				}
			}
		}		
		return FALSE;
	}
	
	public function ricercaCosti($utility, $db, $replace) {
		
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryCosti;		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);		
		$result = $db->getData($sql);
		
		if (pg_num_rows($result) > 0) {
			$_SESSION['costiBilancio'] = $result;
			$_SESSION['numCostiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['costiBilancio']);
			$_SESSION['numCostiTrovati'] = 0;
		}		
		return $result;
	}

	public function ricercaRicavi($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicavi;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['ricaviBilancio'] = $result;
			$_SESSION['numRicaviTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['ricaviBilancio']);
			$_SESSION['numRicaviTrovati'] = 0;
		}
		return $result;
	}

	public function ricercaAttivo($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryAttivo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['attivoBilancio'] = $result;
			$_SESSION['numAttivoTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['attivoBilancio']);
			$_SESSION['numAttivoTrovati'] = 0;
		}
		return $result;
	}

	public function ricercaPassivo($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryPassivo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['passivoBilancio'] = $result;
			$_SESSION['numPassivoTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['passivoBilancio']);
			$_SESSION['numPassivoTrovati'] = 0;
		}
		return $result;
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