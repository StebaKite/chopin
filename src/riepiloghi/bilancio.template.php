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
		$risultato_ricerca = "";
		
		if (isset($_SESSION["registrazioniTrovate"])) {

			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='650'>%ml.desconto%</th>" .
			"		<th width='100'>%ml.costi%</th>" .
			"		<th width='100'>%ml.ricavi%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll'>" .
			"	<table class='result'>" .
			"		<tbody>";
				
			$registrazioniTrovate = $_SESSION["registrazioniTrovate"];
			$numReg = 0;
			$totimpdare = 0;
			$totimpavere = 0;
			$desconto_break = "";
			
			foreach(pg_fetch_all($registrazioniTrovate) as $row) {

				if ($desconto_break == "") {$desconto_break = trim($row['des_conto']); }
				
				if (trim($row['des_conto']) == $desconto_break ) {
					
					if ($row['ind_dareavere'] == "D") {
						$impdare = trim($row['tot_conto']);
						$totimpdare += $impdare;
					}
					else {
						$impavere = trim($row['tot_conto']);
						$totimpavere += $impavere;
					}
				}
				else {
					
					$numReg ++;
					
					$dare = ($impdare > 0) ? number_format($impdare, 2, ',', '.') : "";
					$avere = ($impavere > 0) ? number_format($impavere, 2, ',', '.') : "";
					
					$risultato_ricerca = $risultato_ricerca .
					"<tr>" .
					"	<td width='658' align='left'>" . $desconto_break . "</td>" .
					"	<td width='108' align='right'>" . $dare . "</td>" .
					"	<td width='108' align='right'>" . $avere . "</td>" .
					"</tr>";
						
					$desconto_break = trim($row['des_conto']);
					$impdare = 0;
					$impavere = 0;
					
					if ($row['ind_dareavere'] == "D") {
						$impdare = trim($row['tot_conto']);
						$totimpdare += $impdare;
					}
					else {
						$impavere = trim($row['tot_conto']);
						$totimpavere += $impavere;
					}						
				}				
			}
			$_SESSION['numRegTrovate'] = $numReg;
			$risultato_ricerca = $risultato_ricerca . "</tbody><tfoot>";

			$risultato_ricerca = $risultato_ricerca .
			"<tr>" .
			"	<td width='608' align='right' class='totbilancio'>Totale</td>" .
			"	<td width='108' align='right' class='totbilancio'>" . number_format($totimpdare, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='totbilancio'>" . number_format($totimpavere, 2, ',', '.')  . "</td>" .
			"</tr>";				

			$risultato_ricerca = $risultato_ricerca . "</tfoot></table></div>";
		}
		else {
				
		}
				
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%contoeco-selected%' => ($_SESSION["catconto_sel"] == "Conto Economico") ? "selected" : "",
				'%statopat-selected%' => ($_SESSION["catconto_sel"] == "Stato Patrimoniale") ? "selected" : "",
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%risultato_ricerca%' => $risultato_ricerca
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}	
	
?>