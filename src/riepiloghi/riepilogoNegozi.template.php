<?php

require_once 'riepiloghiComparati.abstract.class.php';

class RiepilogoNegoziTemplate extends RiepiloghiComparatiAbstract {

	private static $_instance = null;

	private static $paginaRiepilogoNegozi = "/riepiloghi/riepilogoNegozi.form.html";
	
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

			self::$_instance = new RiepilogoNegoziTemplate();

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
		
		$form = self::$root . $array['template'] . self::$paginaRiepilogoNegozi;
		
		$risultato_costi = "";
		$risultato_ricavi = "";
		$risultato_esercizio = "";
		$mct = "";
		$bep = "";
		$tabs = "";

		if (isset($_SESSION["costiComparati"]))
			$risultato_costi = $this->makeTableCostiComparati($array, $_SESSION["costiComparati"]);
		
		if (isset($_SESSION["ricaviComparati"]))
			$risultato_ricavi = $this->makeTableRicaviComparati($array, $_SESSION["ricaviComparati"]);

		if (isset($_SESSION["attivoComparati"]))
			$risultato_attivo = $this->makeTableAttivoComparati($array, $_SESSION["attivoComparati"]);					

		if (isset($_SESSION["passivoComparati"]))
			$risultato_passivo = $this->makeTablePassivoComparati($array, $_SESSION["passivoComparati"]);
					
		if (isset($_SESSION["costoVariabile"]) and isset($_SESSION["ricavoVenditaProdotti"]) and isset($_SESSION["costoFisso"])) {
			$mct = $this->makeTableMargineContribuzione($_SESSION['costoVariabile'], $_SESSION['ricavoVenditaProdotti'], $_SESSION['costoFisso']);
			$bep = $this->makeTableBep($_SESSION['costoVariabile'], $_SESSION['ricavoVenditaProdotti'], $_SESSION['costoFisso']);
		}			
			
		/** ******************************************
		 * Costruisco il box delle tabs
		 */
					
		if (($risultato_costi != "") or ($risultato_ricavi != "") or ($risultato_attivo != "") or ($risultato_passivo != "")) {
			
			$tabs  = "	<div class='tabs'>";
			$tabs .= "		<ul>";
			
			if ($risultato_costi != "")   { $tabs .= "<li><a href='#tabs-1'>Costi</a></li>"; }
			if ($risultato_ricavi != "")  { $tabs .= "<li><a href='#tabs-2'>Ricavi</a></li>"; }
			if ($risultato_attivo != "")  { $tabs .= "<li><a href='#tabs-3'>Attivo</a></li>"; }
			if ($risultato_passivo != "") { $tabs .= "<li><a href='#tabs-4'>Passivo</a></li>"; }
			
			$tabs .= "<li><a href='#tabs-5'>" . strtoupper($this->nomeTabTotali(abs($_SESSION['totaleRicavi']), abs($_SESSION['totaleCosti']))) . "</a></li>";

			if ($mct != "") { $tabs .= "<li><a href='#tabs-6'>MCT</a></li>"; }
			if ($bep != "") { $tabs .= "<li><a href='#tabs-7'>BEP</a></li>"; }
			$tabs .= "</ul>";
			
			if ($risultato_costi != "")   { $tabs .= "<div id='tabs-1'>" . $risultato_costi . "</div>"; }
			if ($risultato_ricavi != "")  { $tabs .= "<div id='tabs-2'>" . $risultato_ricavi . "</div>"; }
			if ($risultato_attivo != "")  { $tabs .= "<div id='tabs-3'>" . $risultato_attivo . "</div>"; }
			if ($risultato_passivo != "") { $tabs .= "<div id='tabs-4'>" . $risultato_passivo . "</div>"; }
			
			$tabs .= "<div id='tabs-5'>" . $this->tabellaTotali($this->nomeTabTotali(abs($_SESSION['totaleRicavi']), abs($_SESSION['totaleCosti']))) . "</div>";
			
			if ($mct != "") { $tabs .= "<div id='tabs-6'>" . $mct . "</div>"; }
			if ($bep != "") { $tabs .= "<div id='tabs-7'>" . $bep . "</div>"; }
			
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
				'%tabs%' => $tabs,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf'],
				'%saldiInclusichecked%' => ($_SESSION["saldiInclusi"] == "S") ? "checked" : "",
				'%saldiEsclusichecked%' => ($_SESSION["saldiInclusi"] == "N") ? "checked" : "",
				'%saldiInclusi%' => $_SESSION["saldiInclusi"],
				'%tuttiContichecked%' => ($_SESSION["soloContoEconomico"] == "N") ? "checked" : "",
				'%soloContoEconomicochecked%' => ($_SESSION["soloContoEconomico"] == "S") ? "checked" : "",
				'%soloContoEconomico%' => $_SESSION["soloContoEconomico"]
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

	public function tabellaTotali($tipoTotale) {
	
		if ($tipoTotale == "Utile") {
			
			$risultato_esercizio = 
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>&nbsp;</th>" .
			"		<th width='100'>%ml.brembate%</th>" .
			"		<th width='100'>%ml.trezzo%</th>" .
			"		<th width='100'>%ml.villa%</th>" .
			"		<th width='100'>%ml.totale%</th>" .
			"	</thead>" .
			"<tbody>";

			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi_Bre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi_Tre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi_Vil"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi"], 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti_Bre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti_Tre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti_Vil"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti"], 2, ',', '.') . "</td>" .
			"</tr>";


			$utile_Bre = $_SESSION["totaleRicavi_Bre"] - $_SESSION["totaleCosti_Bre"];
			$utile_Tre = $_SESSION["totaleRicavi_Tre"] - $_SESSION["totaleCosti_Tre"];
			$utile_Vil = $_SESSION["totaleRicavi_Vil"] - $_SESSION["totaleCosti_Vil"];
			$utile = $utile_Bre + $utile_Tre + $utile_Vil;
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($utile_Bre, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($utile_Tre, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($utile_Vil, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($utile, 2, ',', '.') . "</td>" .
			"</tr>";
			
			$risultato_esercizio .= "</tbody></table>" ;
		}
		elseif ($tipoTotale == "Perdita") {
			
			$risultato_esercizio = 
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>&nbsp;</th>" .
			"		<th width='100'>%ml.brembate%</th>" .
			"		<th width='100'>%ml.trezzo%</th>" .
			"		<th width='100'>%ml.villa%</th>" .
			"		<th width='100'>%ml.totale%</th>" .
			"	</thead>" .
			"<tbody>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi_Bre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi_Tre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi_Vil"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleRicavi"], 2, ',', '.') . "</td>" .
			"</tr>";

			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti_Bre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti_Tre"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti_Vil"], 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($_SESSION["totaleCosti"], 2, ',', '.') . "</td>" .
			"</tr>";
				
			$perdita_Bre = $_SESSION["totaleRicavi_Bre"] - $_SESSION["totaleCosti_Bre"];
			$perdita_Tre = $_SESSION["totaleRicavi_Tre"] - $_SESSION["totaleCosti_Tre"];
			$perdita_Vil = $_SESSION["totaleRicavi_Vil"] - $_SESSION["totaleCosti_Vil"];
			$perdita = $perdita_Bre + $perdita_Tre + $perdita_Vil;   			
			
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Perdita del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($perdita_Bre, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($perdita_Tre, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($perdita_Vil, 2, ',', '.') . "</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($perdita, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		
		}
		else {
			
			$risultato_esercizio = "<table class='result'><tbody>";

			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$pareggio = $totaleRicavi - $totaleCosti;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($pareggio, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		}
		return $risultato_esercizio;
	}	
}	
	
?>