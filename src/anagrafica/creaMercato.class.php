<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';

class CreaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	public static $_instance = null;

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

			self::$_instance = new CreaMercato();

		return self::$_instance;
	}

	public function start() {}
	
	public function go() {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaMercato.class.php';
	
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		// Aggiornamento del DB ------------------------------

		if ($this->creaMercato($db, $utility)) {

			unset($_SESSION["codmercato"]);
			unset($_SESSION["desmercato"]);
			unset($_SESSION["cittamercato"]);
			unset($_SESSION["codneg"]);
		}
			
		$ricercaMercato = RicercaMercato::getInstance();
		$ricercaMercato->start();			
	}

	private function creaMercato($db, $utility) {
	
		$codmercato = $_SESSION["codmercato"];
		$desmercato = str_replace("'","''",$_SESSION["desmercato"]);
		$cittamercato = str_replace("'","''",$_SESSION["cittamercato"]);
		$codneg = $_SESSION["codneg"];
		
		if ($this->inserisciMercato($db, $utility, $codmercato, $desmercato, $cittamercato, $codneg)) {
			$_SESSION["messaggioCreazione"] = "Nuovo mercato creato con successo";
			return TRUE;
		}
		else {
			error_log("Errore inserimento mercato");
			unset($_SESSION["messaggioCreazione"]);
			return FALSE;				
		}
	}
}	
	
?>