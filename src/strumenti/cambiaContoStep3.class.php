<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep3 extends StrumentiAbstract {

	private static $_instance = null;

	public static $azioneConferma = "../strumenti/cambiaContoStep3Facade.class.php?modo=go";
	
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
	
			self::$_instance = new CambiaContoStep3();
	
		return self::$_instance;
	}
	
	public function start() {

		require_once 'cambiaContoStep3.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$cambiaContoStep3Template = CambiaContoStep3Template::getInstance();
		$this->preparaPagina($cambiaContoStep3Template);
		
		// compone la pagina
		include($testata);
		$cambiaContoStep3Template->displayPagina();
		include($piede);
	}

	public function go() {
		
		require_once 'cambiaContoStep3.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$cambiaContoStep3Template = CambiaContoStep3Template::getInstance();
		$this->preparaPagina($cambiaContoStep3Template);
		
		// compone la pagina
		include($testata);
		$cambiaContoStep3Template->displayPagina();
		include($piede);
	}
		
	public function preparaPagina($ricercaRegistrazioneTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneConferma;
		$_SESSION["titoloPagina"] = "%ml.cambioContoStep3%";
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	}
}
	
?>