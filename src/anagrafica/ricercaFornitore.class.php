<?php

require_once 'anagrafica.abstract.class.php';

class RicercaFornitore extends AnagraficaAbstract {

	private static $_instance = null;

	public static $azioneRicercaFornitore = "../anagrafica/ricercaFornitoreFacade.class.php?modo=go";
	public static $queryRicercaFornitore = "/anagrafica/ricercaFornitore.sql";

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

			self::$_instance = new RicercaFornitore();

		return self::$_instance;
	}

	public function start() {
	
		require_once 'ricercaFornitore.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		unset($_SESSION["fornitoriTrovati"]);
	
		$ricercaFornitoreTemplate = RicercaFornitoreTemplate::getInstance();
	
		if ($this->ricercaDati($utility)) {
	
			$this->preparaPagina($ricercaFornitoreTemplate);
				
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
			
			$ricercaFornitoreTemplate->displayPagina();
	
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			*/
			if (isset($_SESSION["messaggioCancellazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovati " . $_SESSION['numFornitoriTrovati'] . " fornitori";
				unset($_SESSION["messaggioCancellazione"]);
			}
			else {
				$_SESSION["messaggio"] = "Trovati " . $_SESSION['numFornitoriTrovati'] . " fornitori";
			}
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
	
			if ($_SESSION['numFornitoriTrovati'] > 0) {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			}
	
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
		else {
	
			$this->preparaPagina($ricercaFornitoreTemplate);
				
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaFornitoreTemplate->displayPagina();
	
			$_SESSION["messaggio"] = "Errore fatale durante la lettura dei fornitori" ;
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
	}
	
	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
		
		$replace = array();
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaFornitore;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['fornitoriTrovati'] = $result;
		}
		else {
			unset($_SESSION['fornitoriTrovati']);
			$_SESSION['numFornitoriTrovati'] = 0;
		}
		return $result;
	}
	
	public function preparaPagina() {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaFornitore;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaFornitore%";
	}
}	

?>