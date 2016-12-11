<?php

require_once 'primanota.abstract.class.php';

class ModificaPagamentoTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/modificaPagamento.form.html";

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

			self::$_instance = new ModificaPagamentoTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$array = $utility->getConfig();
		
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
		 * Se è stato immesso un numero fattura allora deve esserci un fornitore
		 */
		if ($_SESSION["numfatt"] != "") {
			if ($_SESSION["fornitore"] == "") {
				$msg = $msg . "<br>&ndash; Col numero fattura presente devi inserire il fornitore";
				$esito = FALSE;
			}
		}
				
		/**
		 * Se è stato immesso un fornitore o un cliente deve esserci un numero fattura
		 */
		if ($_SESSION["fornitore"] != "") {
			if ($_SESSION["numfatt"] == "") {
				$msg = $msg . "<br>&ndash; In presenza di un fornitore deve esserci in numero di fattura";
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
		 * Prepara la tabella dei dettagli del pagamento da iniettare in pagina
		 */
		
		$tbody_dettagli = "";
		$thead_dettagli = "";
		
		$thead_dettagli =
			"<tr>" .
			"<th>Conto</th>" .
			"<th class='dt-right'>Importo</th>" .
			"<th>D/A</th>" .
			"<th>&nbsp;</th>" .
			"</tr>";
		
		$result = $_SESSION["elencoDettagliPagamento"];
	
		$dettaglioPagamento = pg_fetch_all($result);
	
		foreach ($dettaglioPagamento as $row) {
				
			$tbody_dettagli = $tbody_dettagli .
			"<tr>" .
			"<td>" . $row["cod_conto"] . $row["cod_sottoconto"] . " - " . $row["des_sottoconto"] . "</td>" .
			"<td class='dt-right'>" . number_format(trim($row["imp_registrazione"]), 2, ',', '.') . "</td>" .
			"<td class='dt-center'>" . $row["ind_dareavere"] . "</td>" .
			"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagamento(" . $row["id_dettaglio_registrazione"] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
			"</tr>";
		}
	
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%idregistrazione%' => $_SESSION["idRegistrazione"],
				'%idpagamento%' => $_SESSION["idPagamento"],
				'%descreg%' => str_replace("'", "&apos;", $_SESSION["descreg"]),
				'%datareg%' => $_SESSION["datareg"],
				'%numfatt%' => $_SESSION["numfatt"],
				'%desforn%' => $_SESSION["desforn"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%datascad_da%' => $_SESSION["datascad_da"],
				'%datascad_a%' => $_SESSION["datascad_a"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%elenco_scadenze_fornitore%' => $_SESSION["elenco_scadenze_fornitore"],
				'%thead_dettagli%' => $thead_dettagli,
				'%tbody_dettagli%' => $tbody_dettagli
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
}
	
?>		