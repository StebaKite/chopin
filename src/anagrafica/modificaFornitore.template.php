<?php

require_once 'anagrafica.abstract.class.php';

class ModificaFornitoreTemplate extends AnagraficaAbstract {

	private static $_instance = null;

	private static $pagina = "/anagrafica/modificaFornitore.form.html";

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

			self::$_instance = new ModificaFornitoreTemplate();

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
	
		if ($_SESSION["codfornitore"] == "") {
			$msg = $msg . "<br>&ndash; Manca il codice del fornitore";
			$esito = FALSE;
		}

		if ($_SESSION["desfornitore"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del fornitore";
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
				'%codfornitore%' => $_SESSION["codfornitore"],
				'%desfornitore%' => $_SESSION["desfornitore"],
				'%indfornitore%' => $_SESSION["indfornitore"],
				'%cittafornitore%' => $_SESSION["cittafornitore"],
				'%capfornitore%' => $_SESSION["capfornitore"],
				'%bonifico_checked%' => (trim($_SESSION["tipoaddebito"]) == "BONIFICO") ? "checked" : "",
				'%riba_checked%' => (trim($_SESSION["tipoaddebito"]) == "RIBA") ? "checked" : "",
				'%rimdiretta_checked%' => (trim($_SESSION["tipoaddebito"]) == "RIM_DIR") ? "checked" : "",
				'%assegnobancario_checked%' => (trim($_SESSION["tipoaddebito"]) == "ASS_BAN") ? "checked" : "",
				'%addebitodiretto_checked%' => (trim($_SESSION["tipoaddebito"]) == "ADD_DIR") ? "checked" : "",
				'%numggscadenzafattura%' => $_SESSION["numggscadenzafattura"]
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}	
}

?>