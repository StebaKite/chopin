<?php

require_once 'primanota.abstract.class.php';

class ModificaRegistrazioneTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/modificaRegistrazione.form.html";

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

			self::$_instance = new ModificaRegistrazioneTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		
		$esito = TRUE;
		$msg = "<br>";
		unset($_SESSION["esitoControlloDescrizione"]);
		unset($_SESSION["esitoControlloCausale"]);
		unset($_SESSION["esitoControlloFornitore"]);
		unset($_SESSION["esitoControlloCliente"]);
		unset($_SESSION["esitoControlloNumfatt"]);
		unset($_SESSION["esitoControlloDatascad"]);

		$db = Database::getInstance();		
		$utility = Utility::getInstance();
		
		$array = $utility->getConfig();
		
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
					$msg = $msg . "<br>&ndash; Numero fattura gi&agrave; esistente";
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
				
				/**
				 * Controllo la congruenza del fornitore / cliente col conto dei dettagli
				 */
				if ($_SESSION["fornitore"] != "") {					
					if (strstr($array['contiFornitore'], trim(substr($e[0],0,3)))) {

						$conto = trim(substr($e[0],0,3)) . $this->leggiDescrizioneFornitore($db, $utility, $_SESSION["fornitore"]);
						
						if (trim($conto) != $e[0]) {
							$msg .= "<br>&ndash; Il conto in dettaglio non appartiene a questo fornitore";
							$esito = FALSE;								
						}
					}
				}
				else {
					if ($_SESSION["cliente"] != "") {
						if (strstr($array['contiCliente'], trim(substr($e[0],0,3)))) {

							$conto = trim(substr($e[0],0,3)) . $this->leggiDescrizioneCliente($db, $utility, $_SESSION["cliente"]);
								
							if (trim($conto) != $e[0]) {
								$msg .= "<br>&ndash; Il conto in dettaglio non appartiene a questo cliente";
								$esito = FALSE;
							}
						}
					}					
				}
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

		/**
		 * Prepara la tabella delle multiscadenze da iniettare in pagina
		 */

		$theadScadenze = "";
		$tbodyScadenze = "";
		
		if (isset($_SESSION["numeroScadenzeRegistrazione"])) {

			$class_scadenzesuppl = "datiCreateSottile";
				
			$theadScadenze =
			"<tr>" .
			"<th width='100' align='center'>Scadenza</th>" .
			"<th width='100' align='right'>Importo</th>" .
			"<th>&nbsp;</th>" .
			"</tr>";
			
			$scadenzeregistrazione = $_SESSION["elencoScadenzeRegistrazione"];
			
			foreach ($scadenzeregistrazione as $row) {
			
				$tbodyScadenze .=
				"<tr>" .
				"<td align='center'>" . date("d/m/Y",strtotime($row['dat_scadenza'])) . "</td>" .
				"<td align='right'>" . number_format(round($row['imp_in_scadenza'],2), 2, ',', '.') . "</td>" .
				"<td id='icons'><a class='tooltip' onclick='cancellaScadenza(" . $row["id_scadenza"] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
			}
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
				'%datascad%' => $_SESSION["datascad"],
				'%datareg%' => $_SESSION["datareg"],
				'%numfatt%' => trim($_SESSION["numfatt"]),
				'%numfattCurrent%' => trim($_SESSION["numfattCurrent"]),
				'%fornitore%' => $_SESSION["desforn"],
				'%cliente%' => $_SESSION["descli"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%thead_dettagli%' => $thead_dettagli,
				'%tbody_dettagli%' => $tbody_dettagli,
				'%class_scadenzesuppl%' => $class_scadenzesuppl,
				'%thead_scadenze%' => $theadScadenze,
				'%tbody_scadenze%' => $tbodyScadenze,
				'%esitoControlloDescrizione%' => $_SESSION["esitoControlloDescrizione"],
				'%esitoControlloCausale%' => $_SESSION["esitoControlloCausale"],
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
	
	/**
	 * Questo metodo fa l'override del super perchè restituisce il codice del fornitore anzichè il suo ID
	 * @see ChopinAbstract::leggiDescrizioneFornitore()
	 */
	public function leggiDescrizioneFornitore($db, $utility, $desfornitore) : string {
	
		$array = $utility->getConfig();
		$replace = array(
				'%des_fornitore%' => trim($desfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaDescrizioneFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		$rows = pg_fetch_all($result);
	
		foreach($rows as $row) {
			$descrizione_fornitore = $row['cod_fornitore'];
		}
		return $descrizione_fornitore;
	}

	/**
	 * Questo metodo fa l'override del super perchè restituisce il codice del cliente anzichè il suo ID
	 * @see ChopinAbstract::leggiDescrizioneCliente()
	 */
	public function leggiDescrizioneCliente($db, $utility, $descliente) : string {
	
		$array = $utility->getConfig();
		$replace = array(
				'%des_cliente%' => trim($descliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaDescrizioneCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		$rows = pg_fetch_all($result);
	
		foreach($rows as $row) {
			$cod_cliente = $row['cod_cliente'];
		}
		return $cod_cliente;
	}
}
		
?>