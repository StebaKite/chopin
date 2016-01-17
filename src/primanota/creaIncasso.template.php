<?php

require_once 'primanota.abstract.class.php';

class CreaIncassoTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/creaIncasso.form.html";

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

			self::$_instance = new CreaIncassoTemplate();

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
			$msg = $msg . "<br>&ndash; Scegli il negozio";
			$esito = FALSE;
		}
		
		if ($_SESSION["descreg"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione";
			$esito = FALSE;
		}

		if ($_SESSION["causale"] == "") {
			$msg = $msg . "<br>&ndash; Manca la causale";
			$esito = FALSE;
		}

		/**
		 * Se è stato immesso un numero fattura allora deve esserci un cliente
		 */
		if ($_SESSION["numfatt"] != "") {
			if (($_SESSION["cliente"] == "")) {
				$msg = $msg . "<br>&ndash; Col numero fattura presente devi inserire il cliente";
				$esito = FALSE;
			}
		}

		/**
		 * Se è stato immesso un cliente deve esserci un numero fattura
		 */
		if ($_SESSION["cliente"] != "") {
			if ($_SESSION["numfatt"] == "") {
				$msg = $msg . "<br>&ndash; In presenza di un cliente deve esserci in numero di fattura";
				$esito = FALSE;
			}
		}

		/**
		 * Controllo di validità degli importi sui dettagli
		 */
		if ($_SESSION["dettagliInseriti"] == "") {
			$msg = $msg . "<br>&ndash; Mancano i dettagli del pagamento";
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
				
			if ($totale  != 0 ) {
				$msg = $msg . "<br>&ndash; La differenza fra Dare e Avere &egrave; di " . $totale . " &euro;";
				$esito = FALSE;
			}
			else {
				$_SESSION["totaleDare"] = $tot_dare;
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
		 * Prepara la tabella dei dettagli inseriti
		 */
			
		if ($_SESSION['dettagliInseriti'] != "") {

			$class_dettagli = "datiCreateSottile";

			$thead_dettagli =
			"<tr>" .
			"<th width='350' align='left'>Conto</th>" .
			"<th width='100' align='right'>Importo</th>" .
			"<th width='50' align='center'>D/A</th>" .
			"<th>&nbsp;</th>" .
			"</tr>";


			$tbody_dettagli = "";
			$d_x_array = "";

			$d = explode(",", $_SESSION['dettagliInseriti']);
				
			foreach($d as $ele) {

				$e = explode("#",$ele);
				$idconto = substr($e[0], 0, 6);

				$dettaglio =
				"<tr id='" . trim($idconto) . "'>" .
				"<td align='left'>" . $e[0] . "</td>" .
				"<td align='right'>&euro;" . number_format($e[1], 2, ',', '.') . "</td>" .
				"<td align='center'>" . $e[2] . "</td>" .
				"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagina(" . trim($idconto) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";

				$tbody_dettagli = $tbody_dettagli . $dettaglio;

				/**
				 * Prepara la valorizzazione dell'array di pagina per i dettagli inseriti
				 */
				$d_x_array = $d_x_array . "'" . $ele . "',";
			}
		}
			
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%descreg%' => str_replace("'", "&apos;", $_SESSION["descreg"]),
				'%datareg%' => $_SESSION["datareg"],
				'%numfatt%' => $_SESSION["numfatt"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%class_dettagli%' => $class_dettagli,
				'%thead_dettagli%' => $thead_dettagli,
				'%tbody_dettagli%' => $tbody_dettagli,
				'%arrayDettagliInseriti%' => $d_x_array,
				'%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
				'%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%elenco_scadenze_cliente%' => $_SESSION["elenco_scadenze_cliente"]
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
