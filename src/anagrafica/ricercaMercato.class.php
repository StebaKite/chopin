<?php

require_once 'anagrafica.abstract.class.php';

class RicercaMercato extends AnagraficaAbstract {

	private static $_instance = null;

	public static $azioneRicercaMercato = "../anagrafica/ricercaMercatoFacade.class.php?modo=go";
	public static $queryRicercaMercato = "/anagrafica/ricercaMercato.sql";

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

			self::$_instance = new RicercaMercato();

		return self::$_instance;
	}

	public function start() {
	
		require_once 'ricercaMercato.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
	
		unset($_SESSION["mercatiTrovati"]);
	
		$ricercaMercatoTemplate = RicercaMercatoTemplate::getInstance();
	
		if ($this->ricercaDati($utility)) {
	
			$this->preparaPagina($ricercaMercatoTemplate);
	
			include($testata);
			$ricercaMercatoTemplate->displayPagina();

			/**
			 * Gestione messaggio dalla creazione
			 */
			if (isset($_SESSION["messaggioCreazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCreazione"] . "<br>" . "Trovati " . $_SESSION['numMercatiTrovati'] . " mercati";
				unset($_SESSION["messaggioCreazione"]);
			}
			else {
				/**
				 * Gestione del messaggio proveniente dalla cancellazione
				 */
				if (isset($_SESSION["messaggioCancellazione"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovati " . $_SESSION['numMercatiTrovati'] . " mercati";
					unset($_SESSION["messaggioCancellazione"]);
				}
				else {
					$_SESSION["messaggio"] = "Trovati " . $_SESSION['numMercatiTrovati'] . " mercati";
				}
			}
			
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
	
			if ($_SESSION['numMercatiTrovati'] > 0) {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			}
	
			echo $utility->tailTemplate($template);
	
			include($piede);
		}
		else {
	
			$this->preparaPagina($ricercaMercatoTemplate);
	
			include(self::$testata);
			$ricercaMercatoTemplate->displayPagina();
	
			$_SESSION["messaggio"] = "Errore fatale durante la lettura dei mercati" ;
	
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
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaMercato;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['mercatiTrovati'] = $result;
		}
		else {
			unset($_SESSION['mercatiTrovati']);
			$_SESSION['numMercatiTrovati'] = 0;
		}
		return $result;
	}
	
	public function preparaPagina($ricercaCausaleTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaMercato;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaMercato%";	
	}
}
	
?>	