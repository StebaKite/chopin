<?php

require_once 'configurazioni.abstract.class.php';

class CreaCausaleTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/creaCausale.form.html";

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

			self::$_instance = new CreaCausaleTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

		$esito = TRUE;
		$msg = "<br>";
		
		/**
		 * Controllo presenza dati obbligatori
		 */
		
		if ($_SESSION["codcausale"] == "") {
			$msg = $msg . "<br>&ndash; Manca il codice della causale";
			$esito = FALSE;
		}
		else {
			if (!is_numeric($_SESSION["codcausale"])) {
				$msg = $msg . "<br>&ndash; Il codice causale deve essere numerico";
				$esito = FALSE;
			}
			else {
				if ($_SESSION["codcausale"] < 1000) {
					$msg = $msg . "<br>&ndash; Il codice causale deve essere maggiore di 1000";
					$esito = FALSE;
				}
			}
		}
		
		if ($_SESSION["descausale"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione della causale";
			$esito = FALSE;
		}
		
		// ----------------------------------------------
		
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
						
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codcausale%' => $_SESSION["codcausale"],
				'%descausale%' => $_SESSION["descausale"],
				'%catcausale%' => $_SESSION["catcausale"]
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>