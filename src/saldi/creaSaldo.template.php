<?php

require_once 'saldi.abstract.class.php';

class CreaSaldoTemplate extends SaldiAbstract {

	private static $_instance = null;

	private static $pagina = "/saldi/creaSaldo.form.html";

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

			self::$_instance = new CreaSaldoTemplate();

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

		if ($_SESSION["codneg"] == "") {
			$msg = $msg . "<br>&ndash; Manca il negozio";
			$esito = FALSE;
		}

		if ($_SESSION["codconto"] == "") {
			$msg = $msg . "<br>&ndash; Manca il conto";
			$esito = FALSE;
		}

		if ($_SESSION["datsaldo"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di riporto del saldo";
			$esito = FALSE;
		}

		if ($_SESSION["dessaldo"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del riporto saldo";
			$esito = FALSE;
		}

		if ($_SESSION["impsaldo"] == "") {
			$msg = $msg . "<br>&ndash; Manca l'importo del saldo";
			$esito = FALSE;
		}

		if ($_SESSION["dareavere"] == "") {
			$msg = $msg . "<br>&ndash; Manca l'indicatore D/A";
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
				'%codneg%' => $_SESSION["codneg"],
				'%codconto%' => $_SESSION["codconto"],
				'%codsottoconto%' => $_SESSION["codsottoconto"],
				'%datsaldo%' => $_SESSION["datsaldo"],
				'%dessaldo%' => $_SESSION["dessaldo"],
				'%impsaldo%' => $_SESSION["impsaldo"],
				'%inddareavere%' => $_SESSION["inddareavere"],
				'%elenco_conti%' => $_SESSION['elenco_conti'],				
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>