<?php

require_once 'primanota.abstract.class.php';

class CreaRegistrazioneTemplate extends PrimanotaAbstract {

	private static $_instance = null;
	
	private static $pagina = "/primanota/creaRegistrazione.form.html";
	
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
	
			self::$_instance = new CreaRegistrazioneTemplate();
	
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
		unset($_SESSION["esitoControlloFornitore"]);
		unset($_SESSION["esitoControlloCliente"]);
		unset($_SESSION["esitoControlloNumfatt"]);
		unset($_SESSION["esitoControlloDatascad"]);
		
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
		 * Se è stato immesso un numero fattura allora deve esserci un fornitore o cliente
		 */
		if ($_SESSION["numfatt"] != "") {
			if (($_SESSION["fornitore"] == "") && ($_SESSION["cliente"] == "")) {
				$msg = $msg . "<br>&ndash; Inserisci il fornitore o il cliente";
				$_SESSION["esitoControlloFornitore"] = "Dato errato";
				$_SESSION["esitoControlloCliente"] = "Dato errato";				
				$esito = FALSE;
			}
			else {
				if ($_SESSION["esitoNumeroFattura"] != "") {
					$msg = $msg . "<br>&ndash; Il numero fattura gi&agrave; esistente";
					$esito = FALSE;
				}				
			}
		}
		
		/**
		 * Controllo di mutua presenza del fornitore o cliente
		 */
		if (($_SESSION["fornitore"] != "") && ($_SESSION["cliente"] != "")) {
			$msg = $msg . "<br>&ndash; Il fornitore e il cliente sono mutualmente esclusivi";
			$_SESSION["esitoControlloFornitore"] = "Dato errato";
			$_SESSION["esitoControlloCliente"] = "Dato errato";
			$esito = FALSE;
		}

		/**
		 * Se è stato immesso un fornitore o un cliente deve esserci un numero fattura e la data scadenza.
		 * La data scadenza va sempre inserita per poter avere la fattura in scadenziario.
		 */
		if (($_SESSION["fornitore"] != "") || ($_SESSION["cliente"] != "")) {
			
			if ($_SESSION["numfatt"] == "") {
				$msg = $msg . "<br>&ndash; Inserisci il numero di fattura";
				$_SESSION["esitoControlloNumfatt"] = "Dato errato";
				$esito = FALSE;	
			}
			
			if ($_SESSION["datascad"] == "") {
				$msg = $msg . "<br>&ndash; Inserisci una data scadenza";
				$_SESSION["esitoControlloDatascad"] = "Dato errato";
				$esito = FALSE;
			}			
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

		$thead_dettagli = "<tr></tr>";
		$tbody_dettagli = "<tr></tr>";
		$class_scadenzesuppl = "";
		$thead_scadenze = "";
		$tbody_scadenze = "";
		$s_x_array = "";
		$d_x_array = "";
		
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
					"<td>" . $e[0] . "</td>" .
					"<td class='dt-right'>" . number_format($e[1], 2, ',', '.') . "</td>" .
					"<td class='dt-center'>" . $e[2] . "</td>" .
					"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagina(" . trim($idconto) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
					"</tr>";
				
				$tbody_dettagli = $tbody_dettagli . $dettaglio;

				/**
				 * Prepara la valorizzazione dell'array di pagina per i dettagli inseriti
				 */				
				$d_x_array = $d_x_array . "'" . $ele . "',";				
			}
 		}
 		
 		/**
 		 * Prepara la tabella delle multiscadenze
 		 */

 		if ($_SESSION['scadenzeInserite'] != "") {
 		
 			$class_scadenzesuppl = "datiCreateSottile";
 		
 			$thead_scadenze =
 			"<tr>" .
 			"<th width='100' align='center'>Scadenza</th>" .
 			"<th width='100' align='right'>Importo</th>" .
 			"<th>&nbsp;</th>" .
 			"</tr>";
 		
 			$tbody_scadenze = "";
 			$s_x_array = "";
 		
 			$d = explode(",", $_SESSION['scadenzeInserite']);
 				
 			foreach($d as $ele) {
 		
 				$e = explode("#",$ele);
 		
 				$dettaglio =
 				"<tr id=" . $e[0] . ">" .
 				"<td align='center'>" . $e[0] . "</td>" .
 				"<td align='center'>" . $e[1] . "</td>" .
 				"<td align='right'>" . $e[2] . "</td>" .
				"<td id='icons'><a class='tooltip' onclick='cancellaScadenzaSupplementarePagina(" . $e[0] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
 		
 				$tbody_scadenze = $tbody_scadenze . $dettaglio;
 		
 				/**
 				 * Prepara la valorizzazione dell'array di pagina per i dettagli inseriti
 				 */
 				$s_x_array = $s_x_array . "'" . $ele . "',";
 			}
 		}
 		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%descreg%' => str_replace("'", "&apos;", $_SESSION["descreg"]),
				'%datascad%' => $_SESSION["datascad"],
				'%datareg%' => $_SESSION["datareg"],
				'%numfatt%' => $_SESSION["numfatt"],
				'%desforn%' => $_SESSION["fornitore"],
				'%descli%' => $_SESSION["cliente"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%thead_dettagli%' => $thead_dettagli,	
				'%tbody_dettagli%' => $tbody_dettagli,
				'%class_scadenzesuppl%' => $class_scadenzesuppl,
				'%thead_scadenzesuppl%' => $thead_scadenze,
				'%tbody_scadenzesuppl%' => $tbody_scadenze,
				'%arrayScadenzeInserite%' => $s_x_array,
				'%arrayIndexScadenzeInserite%' => $_SESSION["indexScadenzeInserite"],
				'%scadenzeInserite%' => $_SESSION["scadenzeInserite"],
				'%arrayDettagliInseriti%' => $d_x_array,
				'%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
				'%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%esitoControlloDescrizione%' => $_SESSION["esitoControlloDescrizione"],
				'%esitoControlloCausale%' => $_SESSION["esitoControlloCausale"],
				'%esitoControlloNegozio%' => $_SESSION["esitoControlloNegozio"],
				'%esitoControlloFornitore%' => $_SESSION["esitoControlloFornitore"],
				'%esitoControlloCliente%' => $_SESSION["esitoControlloCliente"],
				'%esitoControlloDataRegistrazione%' => $_SESSION["esitoControlloDataRegistrazione"],
				'%esitoControlloNumfatt%' => $_SESSION["esitoControlloNumfatt"],
				'%esitoControlloDatascad%' => $_SESSION["esitoControlloDatascad"],
				'%esitoNumeroFattura%' => $_SESSION["esitoNumeroFattura"]
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);		
	}	
}	

?>
