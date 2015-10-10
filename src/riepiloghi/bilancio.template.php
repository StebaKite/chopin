<?php

require_once 'riepiloghi.abstract.class.php';

class BilancioTemplate extends RiepiloghiAbstract {

	private static $_instance = null;

	private static $pagina = "/riepiloghi/bilancio.form.html";

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
		
		$form = self::$root . $array['template'] . self::$pagina;
		$risultato_costi = "";
		$risultato_ricavi = "";
		$risultato_esercizio = "";
		$tabs = "";
		
		/** ******************************************************
		 * Costruzione della tabella costi
		 */
		
		if (isset($_SESSION["costiBilancio"])) {

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
			$totaleConto = 0;
			
			foreach(pg_fetch_all($costiBilancio) as $row) {

				$totaleSottoconto = trim($row['tot_conto']);
				$totaleCosti += $totaleSottoconto;
				
				$numReg ++;
					
				$importo = ($totaleSottoconto > 0) ? number_format($totaleSottoconto, 2, ',', '.') : "";

				if (trim($row['des_conto']) != $desconto_break ) {

					if ($desconto_break != "") {
						
						$totconto = ($totaleConto > 0) ? number_format($totaleConto, 2, ',', '.') : "";
						
						$risultato_costi .=
						"<tr>" .
						"	<td class='mark' colspan='2' align='right'>Totale " . $desconto_break . "</td>" .
						"	<td class='mark' width='108' align='right'>" . $totconto . "</td>" .
						"</tr>";
						
						$totaleConto = 0;
					}					
						
					$risultato_costi .=
					"<tr>" .
					"	<td width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
					"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
					"	<td width='108' align='right'>" . $importo . "</td>" .
					"</tr>";

					$desconto_break = trim($row['des_conto']);
				}
				else {
					
					$risultato_costi .=
					"<tr>" .
					"	<td width='308' align='left'></td>" .
					"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
					"	<td width='108' align='right'>" . $importo . "</td>" .
					"</tr>";
				}
				$totaleConto += $totaleSottoconto;
			}

			$totconto = ($totaleConto > 0) ? number_format($totaleConto, 2, ',', '.') : "";
			
			$risultato_costi .=
			"<tr>" .
			"	<td class='mark' colspan='2' align='right'>Totale " . $desconto_break . "</td>" .
			"	<td class='mark' width='108' align='right'>" . $totconto . "</td>" .
			"</tr>";				
			
			$_SESSION['numCostiTrovati'] = $numReg;
			$risultato_costi = $risultato_costi . "</tbody>";

			$risultato_costi = $risultato_costi . "</table></div>";
		}

		/** ******************************************************
		 * Costruzione della tabella ricavi
		 */

		if (isset($_SESSION["ricaviBilancio"])) {
		
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
			$totaleConto = 0;
				
			foreach(pg_fetch_all($ricaviBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totaleRicavi += $totaleSottoconto;
				
				$numReg ++;
					
				$importo = ($totaleSottoconto > 0) ? number_format($totaleSottoconto, 2, ',', '.') : "";

				if (trim($row['des_conto']) != $desconto_break ) {
					
					if ($desconto_break != "") {
					
						$totconto = ($totaleConto > 0) ? number_format($totaleConto, 2, ',', '.') : "";
					
						$risultato_ricavi .=
						"<tr>" .
						"	<td class='mark' colspan='2' align='right'>Totale " . $desconto_break . "</td>" .
						"	<td class='mark' width='108' align='right'>" . $totconto . "</td>" .
						"</tr>";
					
						$totaleConto = 0;
					}
						
					$risultato_ricavi .=
					"<tr>" .
					"	<td width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
					"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
					"	<td width='108' align='right'>" . $importo . "</td>" .
					"</tr>";

					$desconto_break = trim($row['des_conto']);
				}
				else {
					
					$risultato_ricavi .=
					"<tr>" .
					"	<td width='308' align='left'></td>" .
					"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
					"	<td width='108' align='right'>" . $importo . "</td>" .
					"</tr>";						
				}
				$totaleConto += $totaleSottoconto;				
			}
			
			$totconto = ($totaleConto > 0) ? number_format($totaleConto, 2, ',', '.') : "";
				
			$risultato_ricavi .=
			"<tr>" .
			"	<td class='mark' colspan='2' align='right'>Totale " . $desconto_break . "</td>" .
			"	<td class='mark' width='108' align='right'>" . $totconto . "</td>" .
			"</tr>";
			
			$_SESSION['numRicaviTrovati'] = $numReg;
			$risultato_ricavi = $risultato_ricavi . "</tbody>";
		
			$risultato_ricavi = $risultato_ricavi . "</table></div>";
		}
				
		/** ******************************************
		 * Costruzione delle tabs
		 */
		
		if (($risultato_costi != "") || ($risultato_ricavi != "") || ($risultato_esercizio = "")) {
			
			$tabs  = "	<div class='tabs'>";
			$tabs .= "		<ul>";
			$tabs .= "			<li><a href='#tabs-1'>Costi</a></li>";
			$tabs .= "			<li><a href='#tabs-2'>Ricavi</a></li>";
			$tabs .= "			<li><a href='#tabs-3'>" . strtoupper($this->nomeTabTotali($totaleRicavi, $totaleCosti)) . "</a></li>";
			$tabs .= "		</ul>";
			$tabs .= "		<div id='tabs-1'>" . $risultato_costi . "</div>";
			$tabs .= "		<div id='tabs-2'>" . $risultato_ricavi . "</div>";
			$tabs .= "		<div id='tabs-3'>" . $this->tabellaTotali($this->nomeTabTotali($totaleRicavi, $totaleCosti), $totaleRicavi, $totaleCosti) . "</div>";
			$tabs .= "	</div>";
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
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf']
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
			"	<td width='108' align='right' class='mark'>" . number_format($totaleCosti, 2, ',', '.') . "</td>" .
			"</tr>";
			
			$utile = $totaleRicavi - $totaleCosti;
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($utile, 2, ',', '.') . "</td>" .
			"</tr>";
			
			$totalePareggio = $totaleCosti + $utile;
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale a Pareggio</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($totalePareggio, 2, ',', '.') . "</td>" .
			"</tr>";
			
			$risultato_esercizio .= "</tbody></table>" ;
		}
		elseif ($tipoTotale == "Perdita") {
			
			$risultato_esercizio = "<table class='result'><tbody>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($totaleRicavi, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$perdita = $totaleCosti - $totaleRicavi;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Perdita del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($perdita, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$totalePareggio = $totaleRicavi + $perdita;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale a Pareggio</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($totalePareggio, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		
		}
		else {
			
			$risultato_esercizio = "<table class='result'><tbody>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($totaleCosti, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$pareggio = $totaleRicavi - $totaleCosti;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($pareggio, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$totalePareggio = $totaleCosti + $pareggio;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale a Pareggio</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($totalePareggio, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		}
		return $risultato_esercizio;
	}	
}	
	
?>