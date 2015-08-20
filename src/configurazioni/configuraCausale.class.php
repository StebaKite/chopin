<?php

require_once 'configurazioni.abstract.class.php';

class ConfiguraCausale extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneModificaConto = "../configurazioni/configuraCausaleFacade.class.php?modo=go";

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

			self::$_instance = new ConfiguraCausale();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'configuraCausale.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$this->prelevaContiCausale($utility);
		$this->prelevaContiDisponibili($utility);
		
		$configuraCausaleTemplate = ConfiguraCausaleTemplate::getInstance();
		$this->preparaPagina($configuraCausaleTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$configuraCausaleTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {}
	
	public function prelevaContiCausale($utility) {

		require_once 'database.class.php';		
		$db = Database::getInstance();
		
		$result = $this->leggiContiCausale($db, $utility, $_SESSION["codcausale"]);
		
		if ($result) {		
			$_SESSION["contiCausale"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo conti configurati per la causale : " . $_SESSION["codcausale"] . " <<<<<<<<" );
		}
	}

	public function prelevaContiDisponibili($utility) {

		require_once 'database.class.php';
		$db = Database::getInstance();
		
		$result = $this->leggiContiDisponibili($db, $utility, $_SESSION["codcausale"]);
		
		if ($result) {
			$_SESSION["contiDisponibili"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo conti disponibili <<<<<<<<" );
		}
	}

	public function preparaPagina($configuraCausaleTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$configuraCausaleTemplate->setTitoloPagina("%ml.configuraCausale%");
	}
	
}

?>