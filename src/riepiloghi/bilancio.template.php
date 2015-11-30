<?php

require_once 'riepiloghi.abstract.class.php';

class BilancioTemplate extends RiepiloghiAbstract {

	private static $_instance = null;

	private static $paginaPeriodico = "/riepiloghi/bilancioPeriodico.form.html";
	private static $paginaEsercizio = "/riepiloghi/bilancioEsercizio.form.html";
	
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

			self::$_instance = new BilancioTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() { return TRUE;}

	public function displayPagina() {

		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		if ($_SESSION["tipoBilancio"] == "Periodico") {
			$form = self::$root . $array['template'] . self::$paginaPeriodico;
		}
		elseif ($_SESSION["tipoBilancio"] == "Esercizio") {
			$form = self::$root . $array['template'] . self::$paginaEsercizio;
		}

		$risultato_costi = "";
		$risultato_ricavi = "";
		$risultato_esercizio = "";
		$tabs = "";
		
		/** ******************************************************
		 * Costruzione della tabella costi
		 */
		
		if (isset($_SESSION["costiBilancio"])) {

			$sottocontiCostiVariabili = ($array['sottocontiCostiVariabili'] != "") ? explode(",", $array['sottocontiCostiVariabili']) : "";
			
			$risultato_costi =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
				
			$costiBilancio = $_SESSION["costiBilancio"];
			$numReg = 0;
			$totaleCosti = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
			
			$totaleCosti_margineContribuzione = 0;
			
			foreach(pg_fetch_all($costiBilancio) as $row) {

				$totaleSottoconto = trim($row['tot_conto']);
				$totaleCosti += $totaleSottoconto;
				
				$numReg ++;
					
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');

				if ($sottocontiCostiVariabili != "") {
					if (in_array(trim($row['des_sottoconto']), $sottocontiCostiVariabili)) {
						$totaleCosti_margineContribuzione += $totaleSottoconto;
					}
				}				
				
				if (trim($row['des_conto']) != $desconto_break ) {

					if ($desconto_break != "") {
						
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
						
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_costi .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_costi .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						
						$totaleConto = 0;
					}					

					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_costi .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}

					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {

					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_costi .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				
				$totaleConto += $totaleSottoconto;
			}

			$totconto = number_format(abs($totaleConto), 2, ',', '.');
				
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_costi .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_costi .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
				
			$_SESSION['numCostiTrovati'] = $numReg;
			$risultato_costi = $risultato_costi . "</tbody>";

			$risultato_costi = $risultato_costi . "</table></div>";

			/**
			 * Metto in sessione il totale costi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totaleCosti'] = $totaleCosti;
		}

		/** ******************************************************
		 * Costruzione della tabella ricavi
		 */

		if (isset($_SESSION["ricaviBilancio"])) {

			$sottocontiRicavi = ($array['sottocontiRicavi'] != "") ? explode(",", $array['sottocontiRicavi']) : "";
				
			$risultato_ricavi =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$ricaviBilancio = $_SESSION["ricaviBilancio"];
			$numReg = 0;
			$totaleRicavi = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
				
			$totaleRicavi_margineContribuzione = 0;
			
			foreach(pg_fetch_all($ricaviBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totaleRicavi += $totaleSottoconto;
				
				$numReg ++;

				if ($sottocontiRicavi != "") {
					if (in_array(trim($row['des_sottoconto']), $sottocontiRicavi)) {
						$totaleRicavi_margineContribuzione += $totaleSottoconto;
					}
				}
				
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');

				if (trim($row['des_conto']) != $desconto_break ) {
					
					if ($desconto_break != "") {
					
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
					
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_ricavi .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_ricavi .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
					
						$totaleConto = 0;
					}
						
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_ricavi .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}

					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
					
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_ricavi .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;				
			}
			
			$totconto = number_format(abs($totaleConto), 2, ',', '.');
				
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_ricavi .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_ricavi .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
				
			$_SESSION['numRicaviTrovati'] = $numReg;
			$risultato_ricavi = $risultato_ricavi . "</tbody>";
		
			$risultato_ricavi = $risultato_ricavi . "</table></div>";
			
			/**
			 * Metto in sessione il totale ricavi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totaleRicavi'] = $totaleRicavi;				
		}

		/** ******************************************************
		 * Costruzione della tabella Attivo
		 */
		
		if (isset($_SESSION["attivoBilancio"])) {
		
			$risultato_attivo =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$attivoBilancio = $_SESSION["attivoBilancio"];
			$numReg = 0;
			$totaleAttivo = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
		
			foreach(pg_fetch_all($attivoBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totaleAttivo += $totaleSottoconto;
		
				$numReg ++;
					
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
		
				if (trim($row['des_conto']) != $desconto_break ) {
						
					if ($desconto_break != "") {
							
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
							
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_attivo .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_attivo .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
							
						$totaleConto = 0;
					}
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_attivo .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
		
					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
						
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_attivo .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;
			}
				
			$totconto = number_format(abs($totaleConto), 2, ',', '.');
		
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_attivo .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_attivo .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
		
			$_SESSION['numAttivoTrovati'] = $numReg;
			$risultato_attivo = $risultato_attivo . "</tbody>";
		
			$risultato_attivo = $risultato_attivo . "</table></div>";
				
			/**
			 * Metto in sessione il totale saldi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totaleAttivo'] = $totaleAttivo;
		}

		/** ******************************************************
		 * Costruzione della tabella Passivo
		 */
		
		if (isset($_SESSION["passivoBilancio"])) {
		
			$risultato_passivo =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$passivoBilancio = $_SESSION["passivoBilancio"];
			$numReg = 0;
			$totalePassivo = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
		
			foreach(pg_fetch_all($passivoBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totalePassivo += $totaleSottoconto;
		
				$numReg ++;
					
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
		
				if (trim($row['des_conto']) != $desconto_break ) {
		
					if ($desconto_break != "") {
							
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
							
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_passivo .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_passivo .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
							
						$totaleConto = 0;
					}
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_passivo .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
		
					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_passivo .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;
			}
		
			$totconto = number_format(abs($totaleConto), 2, ',', '.');
		
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_passivo .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_passivo .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
		
			$_SESSION['numPassivoTrovati'] = $numReg;
			$risultato_passivo = $risultato_passivo . "</tbody>";
		
			$risultato_passivo = $risultato_passivo . "</table></div>";
		
			/**
			 * Metto in sessione il totale saldi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totalePassivo'] = $totalePassivo;
		}
		
		/** ******************************************
		 * Costruisco il box delle tabs
		 */
					
		if (($risultato_costi != "") || ($risultato_ricavi != "") || ($risultato_attivo != "") || ($risultato_passivo != "")) {
			
			/**
			 * Annotazione provvisoria per 2015, con il 2016 puoi buttarla via con la tab-6
			 */
			$nota = "<p>Il bilancio di esercizio, <b>per il 2015</b>, viene generato partendo dal primo saldo disponibile: il <b>01/07/2015</b><br> " .
					"<p>La funzione preleva un parametro dal config 'primoSaldoDisponibile = 01/07/2015' , in situazioni normali questo parametro non è " .
					"valorizzato consentendo alla funzione il prelievo del primo saldo dell'anno al 01/01/2015</p>" .
					"<p>Il bilancio periodico invece è funzionante e può essere estratto sempre tenendo presente la data del primo saldo o le " .
					"eventuali successive.</p>";
			
			$margineContribuzione =
			"<table class='result'>" .
			"	<tbody>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Costi variabili</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCosti_margineContribuzione), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Ricavi vendita prodotti</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi_margineContribuzione), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"   </tbody>" .				
			"</table>" ;
			
			$tabs  = "	<div class='tabs'>";
			$tabs .= "		<ul>";
			
			if ($risultato_costi != "")   { $tabs .= "<li><a href='#tabs-1'>Costi</a></li>"; }
			if ($risultato_ricavi != "")  { $tabs .= "<li><a href='#tabs-2'>Ricavi</a></li>"; }
			if ($risultato_attivo != "")  { $tabs .= "<li><a href='#tabs-3'>Attivo</a></li>"; }
			if ($risultato_passivo != "") { $tabs .= "<li><a href='#tabs-4'>Passivo</a></li>"; }
			
			$tabs .= "<li><a href='#tabs-5'>" . strtoupper($this->nomeTabTotali($totaleRicavi, $totaleCosti)) . "</a></li>";
			$tabs .= "<li><a href='#tabs-6'>Nota importante</a></li>";
			$tabs .= "<li><a href='#tabs-7'>Margine Contribuzione</a></li>";
			$tabs .= "</ul>";
			
			if ($risultato_costi != "")   { $tabs .= "<div id='tabs-1'>" . $risultato_costi . "</div>"; }
			if ($risultato_ricavi != "")  { $tabs .= "<div id='tabs-2'>" . $risultato_ricavi . "</div>"; }
			if ($risultato_attivo != "")  { $tabs .= "<div id='tabs-3'>" . $risultato_attivo . "</div>"; }
			if ($risultato_passivo != "") { $tabs .= "<div id='tabs-4'>" . $risultato_passivo . "</div>"; }
			
			$tabs .= "<div id='tabs-5'>" . $this->tabellaTotali($this->nomeTabTotali($totaleRicavi, $totaleCosti), $totaleRicavi, $totaleCosti) . "</div>";
			$tabs .= "<div id='tabs-6'>" . $nota . "</div>";
			$tabs .= "<div id='tabs-7'>" . $margineContribuzione . "</div>";
			$tabs .= "</div>";				
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%catconto_sel%' => $_SESSION["catconto_sel"],				
				'%contoeco-selected%' => ($_SESSION["catconto_sel"] == "Conto Economico") ? "selected" : "",
				'%statopat-selected%' => ($_SESSION["catconto_sel"] == "Stato Patrimoniale") ? "selected" : "",
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%tabs%' => $tabs,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf'],
				'%saldiInclusichecked%' => ($_SESSION["saldiInclusi"] == "S") ? "checked" : "",
				'%saldiEsclusichecked%' => ($_SESSION["saldiInclusi"] == "N") ? "checked" : "",
				'%saldiInclusi%' => $_SESSION["saldiInclusi"],
				'%ml.anno_esercizio_corrente%' => date("Y"),
				'%ml.anno_esercizio_menouno%' => date("Y")-1,
				'%ml.anno_esercizio_menodue%' => date("Y")-2,
				'%ml.anno_esercizio_menotre%' => date("Y")-3
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
	
	public function nomeTabTotali($totaleRicavi, $totaleCosti) {

		if ($totaleRicavi > $totaleCosti) {
			$nomeTabTotali = "Utile";
		}
		elseif ($totaleRicavi < $totaleCosti) {
			$nomeTabTotali = "Perdita";
		}
		else {
			$nomeTabTotali = "Pareggio";
		}
		return $nomeTabTotali;
	}

	public function tabellaTotali($tipoTotale, $totaleRicavi, $totaleCosti) {
	
		if ($tipoTotale == "Utile") {
			
			$risultato_esercizio = "<table class='result'><tbody>";
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
			"</tr>";
			
			$utile = $totaleRicavi - $totaleCosti;
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($utile), 2, ',', '.') . "</td>" .
			"</tr>";
			
			$totalePareggio = $totaleCosti + $utile;
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale a Pareggio</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totalePareggio), 2, ',', '.') . "</td>" .
			"</tr>";
			
			$risultato_esercizio .= "</tbody></table>" ;
		}
		elseif ($tipoTotale == "Perdita") {
			
			$risultato_esercizio = "<table class='result'><tbody>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$perdita = $totaleCosti - $totaleRicavi;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Perdita del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($perdita), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$totalePareggio = $totaleRicavi + $perdita;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale a Pareggio</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totalePareggio), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		
		}
		else {
			
			$risultato_esercizio = "<table class='result'><tbody>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$pareggio = $totaleRicavi - $totaleCosti;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($pareggio), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$totalePareggio = $totaleCosti + $pareggio;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale a Pareggio</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($totalePareggio), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		}
		return $risultato_esercizio;
	}	
}	
	
?>