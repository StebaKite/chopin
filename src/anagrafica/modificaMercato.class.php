<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';

class ModificaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	private static $_instance = null;

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

			self::$_instance = new ModificaMercato();

		return self::$_instance;
	}

	public function start() {}
	
	public function go() {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaMercato.class.php';
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Aggiornamento del DB ------------------------------

		if ($this->aggiornaMercato($db, $utility)) {

			$_SESSION["messaggioModifica"] = "Mercato modificato con successo";
		}
		$ricercaMercato = RicercaMercato::getInstance();
		$ricercaMercato->start();
	}

	private function aggiornaMercato($db, $utility) {
	
		$idmercato = $_SESSION["idmercato"];
		$codmercato = $_SESSION["codmercato"];
		$desmercato = str_replace("'","''",$_SESSION["desmercato"]);
		$cittamercato = str_replace("'","''",$_SESSION["cittamercato"]);	
		$codneg = $_SESSION["codneg"];
		
		if ($this->updateMercato($db, $utility, $idmercato, $codmercato, $desmercato, $cittamercato, $codneg)) {	
			return TRUE;
		}
		else {
			error_log("Errore aggiornamento cliente, eseguito Rollback");
			return FALSE;
		}
	}
}
	
?>	