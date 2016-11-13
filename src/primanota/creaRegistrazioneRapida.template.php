<?php

require_once 'primanota.abstract.class.php';

class CreaRegistrazioneRapidaTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/creaRegistrazioneRapida.form.html";

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

			self::$_instance = new CreaRegistrazioneRapidaTemplate();

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
				
			$dett = explode("&", $_SESSION['dettagliInseriti']);

			$tot_dare = 0;
			$tot_avere = 0;
			$ele = 0;

			$arrayDettagli = array();
			
			for ($i = 0; $i < count($dett) / 4;	$i++) {
				
				$dettaglio = array();
				
				for ($k = 0; $k < 4; $k++) {
					
					$campo = explode("=",$dett[$ele]);
					
					if ($campo[0] == "importo")  $campo[1] = str_replace("%2C", ".", trim($campo[1]));
					if ($campo[0] == "segno") {
						$campo[1] = strtoupper(trim($campo[1]));
						if ((trim($campo[1]) != "D") && (trim($campo[1]) != "A")) {							
							$campo[1] = "";		// se c'è un carattere diverso lo pulisco così squadrerà il calcolo
						}
					}
					
					if ($campo[0] == "desconto") $campo[1] = str_replace("+", " ", trim($campo[1]));
						
					array_push($dettaglio, $campo[1]);
					$ele++;
				}
				array_push($arrayDettagli, $dettaglio);
				unset($dettaglio);
			}

			$_SESSION['arrayDettagli'] = $arrayDettagli;
			
			foreach($arrayDettagli as $ele) {

				if ($ele[3] == "D") {	$tot_dare = $tot_dare + $ele[2]; }
				if ($ele[3] == "A") {	$tot_avere = $tot_avere + $ele[2]; }					
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

		$class_scadenzesuppl = "";
		$thead_scadenze = "";
		$tbody_scadenze = "";
		$s_x_array = "";

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = self::$root . $array['template'] . self::$pagina;

		/**
		 * Prepara la tabella dei dettagli inseriti
		 */
		if (isset($_SESSION['arrayDettagli'])) {
			
			$thead_dettagli =
				"<thead>" .
					"<tr>" .
						"<th>Conto</th>" .
						"<th class='dt-right'>Importo</th>" .
						"<th>D/A</th>" .
						"<th>&nbsp;</th>" .
					"</tr>" .
				"</thead>";
			
			$tbody_dettagli = "<tbody>";
			
			foreach($_SESSION['arrayDettagli'] as $det) {
	
				$tbody_dettagli .=
				"<tr id='" . $det[0] . "'>" .
					"<td>" . str_replace("+", " ", $det[1]) .
						"<input type='hidden' id='conto' name='conto' value='" . $det[0] . "' />" .
						"<input type='hidden' id='desconto' name='desconto' value='" . $det[1] . "' />" .
					"</td>" .
					"<td class='dt-right'>" .
						"<input type='text' id='importo' name='importo' size='10' maxlength='10' value='" . $det[2] . "' />" .
					"</td>" .
					"<td class='dt-center'>" .
						"<input type='text' id='segno' name='segno' size='2' maxlength='1' value='" . $det[3] . "' />" .
					"</td>" .
					"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagina(" . trim($idconto) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
			}			
			$tbody_dettagli .= "</tbody>";
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
				'%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
				'%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
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
