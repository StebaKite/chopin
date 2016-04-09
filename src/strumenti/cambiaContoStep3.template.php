<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep3Template extends StrumentiAbstract {

	private static $_instance = null;

	private static $pagina = "/strumenti/cambiaContoStep3.form.html";

	//-----------------------------------------------------------------------------

	function __construct() {
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CambiaContoStep3Template();

		return self::$_instance;
	}

	// template ------------------------------------------------
	
	public function inizializzaPagina() {}
	
	public function controlliLogici() {
		
		$esito = TRUE;
		$msg = "<br>";
		
		if ($msg != "<br>") {
			$_SESSION["messaggio"] = $msg;
		}
		else {
			unset($_SESSION["messaggio"]);
		}
		
		return $esito;
	}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	
		// Template --------------------------------------------------------------
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;
		$bottone_conferma = "";
		$bottone_conferma = "<button class='button' title='%ml.confermaTip%' >%ml.conferma%</button>";
		
		$numRegSel = $_SESSION['numRegTrovate'];
		$contoDest = $_SESSION['conto_sel_nuovo'];		
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%conto_sel%' => $_SESSION["conto_sel"],
				'%confermaTip%' => $_SESSION["confermaTip"],				
				'%numRegSel%' => $_SESSION['numRegTrovate'],
				'%conto_sel_nuovo%' => $_SESSION['conto_sel_nuovo'],
				'%bottoneConferma%' => $bottone_conferma
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}	

?>