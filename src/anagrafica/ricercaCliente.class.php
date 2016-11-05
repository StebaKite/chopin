<?php

require_once 'anagrafica.abstract.class.php';

class RicercaCliente extends AnagraficaAbstract {

	private static $_instance = null;

	public static $azioneRicercaCliente = "../anagrafica/ricercaClienteFacade.class.php?modo=go";
	public static $queryRicercaCliente = "/anagrafica/ricercaCliente.sql";

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

			self::$_instance = new RicercaCliente();

		return self::$_instance;
	}

	public function start() {
	
		require_once 'ricercaCliente.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		unset($_SESSION["clientiTrovati"]);
	
		$ricercaClienteTemplate = RicercaClienteTemplate::getInstance();
	
		if ($this->ricercaDati($utility)) {
	
			$this->preparaPagina($ricercaClienteTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaClienteTemplate->displayPagina();
	
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			*/
			if (isset($_SESSION["messaggioCancellazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovati " . $_SESSION['numClientiTrovati'] . " clienti";
				unset($_SESSION["messaggioCancellazione"]);
			}
			else {
				$_SESSION["messaggio"] = "Trovati " . $_SESSION['numClientiTrovati'] . " clienti";
			}
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
	
			if ($_SESSION['numClientiTrovati'] > 0) {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			}
	
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
		else {
	
			$this->preparaPagina($ricercaClienteTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaClienteTemplate->displayPagina();
	
			$_SESSION["messaggio"] = "Errore fatale durante la lettura dei clienti" ;
	
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
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaCliente;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['clientiTrovati'] = $result;
		}
		else {
			unset($_SESSION['clientiTrovati']);
			$_SESSION['numClientiTrovati'] = 0;
		}
		return $result;
	}
	
	public function preparaPagina($ricercaCausaleTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaCliente;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaCliente%";

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle categorie -------------------------------------------------------------
		
		$_SESSION['elenco_categorie_cliente'] = $this->caricaCategorieCliente($utility, $db);
		
	}
}

?>