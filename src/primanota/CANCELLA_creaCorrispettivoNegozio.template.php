<?php

require_once 'primanota.abstract.class.php';

class CreaCorrispettivoNegozioTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/creaCorrispettivoNegozio.form.html";

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

			self::$_instance = new CreaCorrispettivoNegozioTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {
	
		$esito = TRUE;
		$msg = "<br>";
		unset($_SESSION["esitoControlloNegozio"]);
		unset($_SESSION["esitoControlloDescrizione"]);
		unset($_SESSION["esitoControlloCausale"]);
		unset($_SESSION["esitoControlloNegozio"]);
		
		/**
		 * Controllo di validita della data registrazione.
		 * La data registrazione viene verificata da una funzione ajax che effettua una verifica di ammissione.
		 */
		if ($_SESSION["esitoControlloDataRegistrazione"] != "") {
			$msg = $msg . "<br>&ndash; La data registrazione non è ammessa";
			$esito = FALSE;
		}
		
		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($_SESSION["codneg"] == "") {
			$msg = $msg . "<br>&ndash; Scegli il negozio";
			$_SESSION["esitoControlloNegozio"] = "Dato errato";
			$esito = FALSE;
		}
		
		if ($_SESSION["descreg"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione";
			$_SESSION["esitoControlloDescrizione"] = "Dato errato";
			$esito = FALSE;
		}
		
		if ($_SESSION["causale"] == "") {
			$msg = $msg . "<br>&ndash; Manca la causale";
			$_SESSION["esitoControlloCausale"] = "Dato errato";
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
				
			if ($totale  != 0 ) {
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

		$thead_dettagli = "<tr></tr>";
		$tbody_dettagli = "<tr></tr>";
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;
	
		/**
		 * Prepara la tabella dei dettagli inseriti
		 */
			
		if ($_SESSION['dettagliInseriti'] != "") {

			$thead_dettagli =
			"<tr>" .
			"<th>Conto</th>" .
			"<th class='dt-right'>Importo</th>" .
			"<th>D/A</th>" .
			"</tr>";
	
			$tbody_dettagli = "";
			$d_x_array = "";
	
			$d = explode(",", $_SESSION['dettagliInseriti']);
				
			foreach($d as $ele) {
	
				$e = explode("#",$ele);
				$idconto = substr($e[0], 0, 6);

				$dettaglio =
				"<tr id='" . trim($idconto) . "'>" .
				"<td>" . $e[0] . "</td>" .
				"<td class='dt-right'>" . number_format($e[1], 2, ',', '.') . "</td>" .
				"<td class='dt-center'>" . $e[2] . "</td>" .
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
				'%conto_erario%' => $_SESSION["conto_erario_negozi"],
				'%conto_corrispettivo%' => $_SESSION["conto_corrispettivo_negozi"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%thead_dettagli%' => $thead_dettagli,
				'%tbody_dettagli%' => $tbody_dettagli,
				'%arrayDettagliInseriti%' => $d_x_array,
				'%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
				'%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%esitoControlloDescrizione%' => $_SESSION["esitoControlloDescrizione"],
				'%esitoControlloCausale%' => $_SESSION["esitoControlloCausale"],
				'%esitoControlloNegozio%' => $_SESSION["esitoControlloNegozio"],
				'%esitoControlloDataRegistrazione%' => $_SESSION["esitoControlloDataRegistrazione"]
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>