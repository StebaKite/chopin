<?php

require_once 'riepiloghi.abstract.class.php';

class AndamentoNegozi extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $azioneAndamentoNegozi = "../riepiloghi/andamentoNegoziFacade.class.php?modo=go";

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

			self::$_instance = new AndamentoNegozi();

			return self::$_instance;
	}


	public function start() {

		require_once 'andamentoNegozi.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");

		$andamentoNegoziTemplate = AndamentoNegoziTemplate::getInstance();
		$this->preparaPagina($andamentoNegoziTemplate);

		// compongo la pagina
		
		include($testata);
		$andamentoNegoziTemplate->displayPagina();
		include($piede);
	}

	public function go() {
		
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		unset($_SESSION["totaliProgressivi"]);
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => ($_SESSION["codneg_sel"] == "") ? "'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'"
		);
	
		$db = Database::getInstance();
		
		
		
		
		
		
		$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
		
		
		
		
		
		
	}

	public function preparaPagina($bilancioTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["confermaTip"] = "%ml.confermaEstraiRiepilogo%";
		$_SESSION["azione"] = self::$azioneAndamentoNegozi;
		$_SESSION["titoloPagina"] = "%ml.andamentoNegozi%";
	}
}

?>
	