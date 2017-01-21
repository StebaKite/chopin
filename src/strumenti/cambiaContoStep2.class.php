<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep2 extends StrumentiAbstract {

	private static $_instance = null;

	public static $azioneSelezioneNuovoConto = "../strumenti/cambiaContoStep3Facade.class.php?modo=start";
		
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
	
			self::$_instance = new CambiaContoStep2();
	
		return self::$_instance;
	}
	
	public function start() {

		require_once 'cambiaContoStep2.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		unset($_SESSION["conto_sel_nuovo"]);
		
		$cambiaContoStep2Template = CambiaContoStep2Template::getInstance();
		$this->preparaPagina($cambiaContoStep2Template);
		
		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$cambiaContoStep2Template->displayPagina();
		include(self::$piede);
	}

	public function go() {}
		
	public function preparaPagina($ricercaRegistrazioneTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneSelezioneNuovoConto;
		$_SESSION["titoloPagina"] = "%ml.cambioContoStep2%";
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		// Prelievo dei dati per popolare i combo -------------------------------------------------------------
		
		$_SESSION['elenco_conti'] = $this->caricaTuttiConti($utility, $db);
	}
}
	
?>