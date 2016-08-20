<?php

require_once 'primanota.abstract.class.php';

class ModificaCorrispettivoTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/modificaCorrispettivo.form.html";

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
			self::$_instance = new ModificaCorrispettivoTemplate();

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

		if ($_SESSION["descreg"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione";
			$esito = FALSE;
		}

		if ($_SESSION["causale"] == "") {
			$msg = $msg . "<br>&ndash; Manca la causale";
			$esito = FALSE;
		}

		/**
		 * Controllo di validità degli importi sui dettagli
		 */
		
		if ($_SESSION["dettagliInseriti"] == "") {
			$msg = $msg . "<br>&ndash; Mancano i dettagli della registrazione";
			$esito = FALSE;
		}
		else {
				
			$d = explode(",", $_SESSION['dettagliInseriti']);
			$tot_dare = 0;
			$tot_avere = 0;

			foreach($d as $ele) {
					
				$e = explode("#",$ele);
				if ($e[2] == "D") {	$tot_dare = $tot_dare + $e[1]; }
				if ($e[2] == "A") {	$tot_avere = $tot_avere + $e[1]; }
			}

			$totale = round($tot_dare, 2) - round($tot_avere, 2);
				
			if (round($totale, 2)  != 0 ) {
				$msg = $msg . "<br>&ndash; La differenza fra Dare e Avere &egrave; di " . round($totale, 2) . " &euro;";
				$esito = FALSE;
				$_SESSION["stareg"] = '02';
			}
			else {
				$_SESSION["totaleDare"] = $tot_dare;
				$_SESSION["stareg"] = '00';
			}
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

		$thead_dettagli =
			"<tr>" .
			"<th>Conto</th>" .
			"<th class='dt-right'>Importo</th>" .
			"<th>D/A</th>" .
			"<th>&nbsp;</th>" .
			"</tr>";
		
		$result = $_SESSION["elencoDettagliRegistrazione"];
				
		$dettaglioregistrazione = pg_fetch_all($result);
		$tbody_dettagli = "";
		$class_scadenzesuppl = "";
		
		foreach ($dettaglioregistrazione as $row) {
		
			$tbody_dettagli = $tbody_dettagli .
				"<tr>" .
					"<td>" . $row["cod_conto"] . $row["cod_sottoconto"] . " - " . $row["des_sottoconto"] . "</td>" .
					"<td class='dt-right'>" . number_format(trim($row["imp_registrazione"]), 2, ',', '.') . "</td>" .
					"<td class='dt-center'>" . $row["ind_dareavere"] . "</td>" .
					"<td id='icons'><a class='tooltip' onclick='cancellaDettaglio(" . $row["id_dettaglio_registrazione"] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
		}
				
		$replace = array(
			'%titoloPagina%' => $this->getTitoloPagina(),
			'%referer%' => $_SESSION['referer_function_name'],
			'%datascad_da%' => $_SESSION["datascad_da"],
			'%datascad_a%' => $_SESSION["datascad_a"],				
			'%azione%' => $this->getAzione(),
			'%confermaTip%' => $this->getConfermaTip(),
			'%idregistrazione%' => $_SESSION["idRegistrazione"],
			'%descreg%' => str_replace("'", "&apos;", trim($_SESSION["descreg"])),
			'%datareg%' => $_SESSION["datareg"],
			'%codneg_sel%' => $_SESSION["codneg_sel"],
			'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
			'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
			'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
			'%datareg_da%' => $_SESSION["datareg_da"],
			'%datareg_a%' => $_SESSION["datareg_a"],
			'%elenco_causali%' => $_SESSION["elenco_causali"],
			'%elenco_conti%' => $_SESSION["elenco_conti"],
			'%elenco_mercati%' => $_SESSION["elenco_mercati"],
			'%thead_dettagli%' => $thead_dettagli,
			'%tbody_dettagli%' => $tbody_dettagli,				
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}		
}
		
?>