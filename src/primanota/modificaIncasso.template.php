<?php

require_once 'primanota.abstract.class.php';

class ModificaIncassoTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/modificaIncasso.form.html";

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

			self::$_instance = new ModificaIncassoTemplate();

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
		 * Se è stato immesso un numero fattura allora deve esserci un cliente
		 */
		if ($_SESSION["numfatt"] != "") {
			if ($_SESSION["cliente"] == "") {
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
			$msg = $msg . "<br>&ndash; Mancano i dettagli dell'incasso";
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
		 * Prepara la tabella dei dettagli dell'incasso da iniettare in pagina
		 */
	
		$result = $_SESSION["elencoDettagliIncasso"];
	
		$dettaglioIncasso = pg_fetch_all($result);
		$tbodyDettagli = "";
	
		foreach ($dettaglioIncasso as $row) {
	
			$tbodyDettagli = $tbodyDettagli .
			"<tr id='" . $row["id_dettaglio_registrazione"] . "'>" .
			"<td align='left'>" . $row["cod_conto"] . $row["cod_sottoconto"] . " - " . $row["des_sottoconto"] . "</td>" .
			"<td align='right'>&euro;" . number_format(trim($row["imp_registrazione"]), 2, ',', '.') . "</td>" .
			"<td align='center'>" . $row["ind_dareavere"] . "</td>" .
			"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioIncasso(" . $row["id_dettaglio_registrazione"] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
			"</tr>";
		}
	
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%idregistrazione%' => $_SESSION["idRegistrazione"],
				'%idincasso%' => $_SESSION["idIncasso"],
				'%descreg%' => $_SESSION["descreg"],
				'%datareg%' => $_SESSION["datareg"],
				'%numfatt%' => $_SESSION["numfatt"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%elenco_scadenze_cliente%' => $_SESSION["elenco_scadenze_cliente"],
				'%tbody_dettagli%' => $tbodyDettagli
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>		