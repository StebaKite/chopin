<?php

require_once 'anagrafica.abstract.class.php';

class ModificaClienteTemplate extends AnagraficaAbstract {

	private static $_instance = null;

	private static $pagina = "/anagrafica/modificaCliente.form.html";

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

			self::$_instance = new ModificaClienteTemplate();

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
	
		if ($_SESSION["codcliente"] == "") {
			$msg = $msg . "<br>&ndash; Manca il codice del cliente";
			$esito = FALSE;
		}

		if ($_SESSION["descliente"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del cliente";
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
				'%codcliente%' => $_SESSION["codcliente"],
				'%descliente%' => $_SESSION["descliente"],
				'%indcliente%' => $_SESSION["indcliente"],
				'%cittacliente%' => $_SESSION["cittacliente"],
				'%capcliente%' => $_SESSION["capcliente"],
				'%bonifico_checked%' => (trim($_SESSION["tipoaddebito"]) == "BONIFICO") ? "checked" : "",
				'%riba_checked%' => (trim($_SESSION["tipoaddebito"]) == "RIBA") ? "checked" : "",
				'%rimdiretta_checked%' => (trim($_SESSION["tipoaddebito"]) == "RIM_DIR") ? "checked" : "",
				'%assegnobancario_checked%' => (trim($_SESSION["tipoaddebito"]) == "ASS_BAN") ? "checked" : "",
				'%addebitodiretto_checked%' => (trim($_SESSION["tipoaddebito"]) == "ADD_DIR") ? "checked" : ""
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}	
}

?>