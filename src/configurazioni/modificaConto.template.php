<?php

require_once 'configurazioni.abstract.class.php';

class ModificaContoTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/modificaConto.form.html";

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

			self::$_instance = new ModificaContoTemplate();

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
	
		if ($_SESSION["desconto"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del conto";
			$esito = FALSE;
		}
		
		if ($_SESSION["sottocontiInseriti"] == "") {
			$msg = $msg . "<br>&ndash; Mancano i sottoconti";
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
	
		/**
		 * Prepara la tabella dei dettagli della registrazione da iniettare in pagina
		 */
	
		$result = $_SESSION["elencoSottoconti"];
	
		$elencoSottoconti = pg_fetch_all($result);
		$tbodySottoconti = "";
	
		foreach ($elencoSottoconti as $row) {
	
			$bottoneCancella = "<td align='right'>" . $row["totale_registrazioni_sottoconto"] . "</td>";
			
			if ($row["totale_registrazioni_sottoconto"] == 0) {
				$bottoneCancella = "<td id='icons'><a class='tooltip' onclick='cancellaSottoconto(" . $row["cod_sottoconto"] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
			}
			
			$tbodySottoconti = $tbodySottoconti .
			"<tr id='" . $row["cod_conto"] . "'>" .
			"<td align='center'>" . $row["cod_sottoconto"] . "</td>" .
			"<td align='left'>" . $row["des_sottoconto"] . "</td>" .			
			$bottoneCancella .
			"</tr>";
		}
		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codconto%' => $_SESSION["codconto"],
				'%desconto%' => $_SESSION["desconto"],
				'%contoeco_checked%' => (trim($_SESSION["catconto"]) == "Conto Economico") ? "checked" : "",
				'%contopat_checked%' => (trim($_SESSION["catconto"]) == "Stato Patrimoniale") ? "checked" : "",
				'%dare_checked%' => (trim($_SESSION["tipconto"]) == "Dare") ? "checked" : "",
				'%avere_checked%' => (trim($_SESSION["tipconto"]) == "Avere") ? "checked" : "",
				'%categoria%' => $_SESSION["categoria"],
				'%tipoconto%' => $_SESSION["tipoconto"],
				'%tbody_sottoconti%' => $tbodySottoconti
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>