<?php

require_once 'riepiloghi.abstract.class.php';

abstract class RiepiloghiComparatiAbstract extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryCreaRegistrazione = "/riepilogho/estraiRegistrazioniBilancio.sql";
	public static $queryCostiComparati = "/riepiloghi/costiComparati.sql";
	public static $queryCostiComparatiConSaldi = "/riepiloghi/costiComparatiConSaldi.sql";
	public static $queryRicaviComparati = "/riepiloghi/ricaviComparati.sql";
	public static $queryRicaviComparatiConSaldi = "/riepiloghi/ricaviComparatiConSaldi.sql";
	public static $queryAttivoComparati = "/riepiloghi/attivoComparati.sql";
	public static $queryPassivoComparati = "/riepiloghi/passivoComparati.sql";
	
	

	function __construct() {
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new PrimanotaAbstract();

		return self::$_instance;
	}

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima note ---------------------------

	/**
	 * Questo metodo estrae i costi di tutti i negozi e mette il resultset in sessione
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaCostiComparati($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiComparatiConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiComparati;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['costiComparati'] = $result;
			$_SESSION['numCostiComparatiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['costiComparati']);
			$_SESSION['numCostiComparatiTrovati'] = 0;
		}
		return $result;
	}

	/**
	 * Questo metodo estrae i ricavi di tutti i negozi e mette il resultset in sessione
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaRicaviComparati($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicaviComparatiConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicaviComparati;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['ricaviComparati'] = $result;
			$_SESSION['numRicaviComparatiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['ricaviComparati']);
			$_SESSION['numRicaviComparatiTrovati'] = 0;
		}
		return $result;
	}
	
	/**
	 * Questo metodo estrae le attivita' di tutti i negozi e mette il resultset in sessione
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 */
	public function ricercaAttivoComparati($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryAttivoComparati;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['attivoComparati'] = $result;
			$_SESSION['numAttivoComparatiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['attivoComparati']);
			$_SESSION['numAttivoComparatiTrovati'] = 0;
		}
		return $result;
	}
	
	/**
	 * Questo metodo estrae le passività di tutti i negozi e mette il resultset in sessione
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 */
	public function ricercaPassivoComparati($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryPassivoComparati;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['passivoComparati'] = $result;
			$_SESSION['numPassivoComparatiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['passivoComparatiBilancio']);
			$_SESSION['numPassivoComparatiTrovati'] = 0;
		}
		return $result;
	}

// 	public function ricercaCostiMargineContribuzione($utility, $db, $replace) {	
// 		return true;
// 	}
	
// 	public function ricercaRicaviMargineContribuzione($utility, $db, $replace) {
// 		return true;
// 	}
	
// 	public function ricercaCostiFissi($utility, $db, $replace) {		
// 		return true;
// 	}
		
	/**
	 * Questo metodo costruisce una tabella html dei costi comparati
	 * @param unknown $dati
	 */
	public function makeTableCostiComparati($array, $dati) {
		
		$sottocontiCostiVariabili = ($array['sottocontiCostiVariabili'] != "") ? explode(",", $array['sottocontiCostiVariabili']) : "";
			
		$risultato_costi =
		"<table class='result'>" .
		"	<thead>" .
		"		<th width='300'>%ml.desconto%</th>" .
		"		<th width='100'>%ml.brembate%</th>" .
		"		<th width='100'>%ml.trezzo%</th>" .
		"		<th width='100'>%ml.villa%</th>" .
		"		<th width='100'>%ml.totale%</th>" .
		"	</thead>" .
		"</table>" .
		"<div class='scroll-riepilogo-negozi'>" .
		"	<table class='result'>" .
		"		<tbody>";
	
		$numReg = 0;
		$totaleCosti = 0;
		$desconto_break = "";
		
		$totaleConto_Bre = 0;
		$totaleConto_Tre = 0;
		$totaleConto_Vil = 0;
		
		$totale_Bre = 0;
		$totale_Tre = 0;
		$totale_Vil = 0;
			
		foreach(pg_fetch_all($dati) as $row) {
	
			$totaleConto = trim($row['tot_conto']);
			$totaleCosti += $totaleConto;
					
			if (trim($row['cod_negozio']) == "BRE") $totale_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totale_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totale_Vil += $totaleConto;
				
			$numReg ++;
	
			if (trim($row['des_conto']) != $desconto_break ) {
	
				if ($desconto_break != "") {

					$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";

					$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
					$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					
					$risultato_costi .=
					"<tr>" .
					"	<td width='308' align='left'>" . $desconto_break . "</td>" .
					"	<td width='108' align='right'>" . $totBre . "</td>" .
					"	<td width='108' align='right'>" . $totTre . "</td>" .
					"	<td width='108' align='right'>" . $totVil . "</td>" .
					"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
					"</tr>";
					
					$totaleConto_Bre = 0;
					$totaleConto_Tre = 0;
					$totaleConto_Vil = 0;
				}	
				
				$desconto_break = trim($row['des_conto']);
			}
			
			if (trim($row['cod_negozio']) == "BRE") $totaleConto_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totaleConto_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totaleConto_Vil += $totaleConto;
		}
		
		$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_costi .=
		"<tr>" .
		"	<td width='308' align='left'>" . $desconto_break . "</td>" .
		"	<td width='108' align='right'>" . $totBre . "</td>" .
		"	<td width='108' align='right'>" . $totTre . "</td>" .
		"	<td width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";

		/**
		 * Totale complessivo di colonna
		 */

		$totBre = ($totale_Bre != 0) ? number_format(abs($totale_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totale_Tre != 0) ? number_format(abs($totale_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totale_Vil != 0) ? number_format(abs($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totale_Bre + $totale_Tre + $totale_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

		$risultato_costi .=
		"<tr>" .
		"	<td class='enlarge' width='308' align='left'>%ml.totale% %ml.costi%</td>" .
		"	<td class='mark' width='108' align='right'>" . $totBre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totTre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		
		
		$_SESSION['numCostiTrovati'] = $numReg;
		$risultato_costi = $risultato_costi . "</tbody>";
	
		$risultato_costi = $risultato_costi . "</table></div>";
	
		/**
		 * Metto in sessione il totale costi di ciascun negozio 
		 */
		
		$_SESSION['totaleCosti_Bre'] = abs($totale_Bre);
		$_SESSION['totaleCosti_Tre'] = abs($totale_Tre);
		$_SESSION['totaleCosti_Vil'] = abs($totale_Vil);
		$_SESSION['totaleCosti'] = abs($totale);
		
		return $risultato_costi;
	}
	
	/**
	 * Questo metodo costruisce una tabella html dei ricavi comparati 
	 * @param unknown $array
	 * @param unknown $dati
	 */
	public function makeTableRicaviComparati($array, $dati) {
		
		$sottocontiRicavi = ($array['sottocontiRicavi'] != "") ? explode(",", $array['sottocontiRicavi']) : "";
	
		$risultato_ricavi =
		"<table class='result'>" .
		"	<thead>" .
		"		<th width='300'>%ml.desconto%</th>" .
		"		<th width='100'>%ml.brembate%</th>" .
		"		<th width='100'>%ml.trezzo%</th>" .
		"		<th width='100'>%ml.villa%</th>" .
		"		<th width='100'>%ml.totale%</th>" .
		"	</thead>" .
		"</table>" .
		"<div class='scroll-riepilogo-negozi'>" .
		"	<table class='result'>" .
		"		<tbody>";
		
		$numReg = 0;
		$totaleRicavi = 0;
		$desconto_break = "";
		
		$totaleConto_Bre = 0;
		$totaleConto_Tre = 0;
		$totaleConto_Vil = 0;
		
		$totale_Bre = 0;
		$totale_Tre = 0;
		$totale_Vil = 0;
			
		foreach(pg_fetch_all($dati) as $row) {
				
			$totaleConto = trim($row['tot_conto']);
			$totaleRicavi += $totaleConto;

			if (trim($row['cod_negozio']) == "BRE") $totale_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totale_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totale_Vil += $totaleConto;
			
			$numReg ++;
			
			if (trim($row['des_conto']) != $desconto_break ) {
			
				if ($desconto_break != "") {
			
					$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
			
					$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
					$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

					$risultato_ricavi .=
					"<tr>" .
					"	<td width='308' align='left'>" . $desconto_break . "</td>" .
					"	<td width='108' align='right'>" . $totBre . "</td>" .
					"	<td width='108' align='right'>" . $totTre . "</td>" .
					"	<td width='108' align='right'>" . $totVil . "</td>" .
					"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
					"</tr>";

					$totaleConto_Bre = 0;
					$totaleConto_Tre = 0;
					$totaleConto_Vil = 0;
				}
					
				$desconto_break = trim($row['des_conto']);
			}
						
			if (trim($row['cod_negozio']) == "BRE") $totaleConto_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totaleConto_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totaleConto_Vil += $totaleConto;
		}		
						
		$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_ricavi .=
		"<tr>" .
		"	<td width='308' align='left'>" . $desconto_break . "</td>" .
		"	<td width='108' align='right'>" . $totBre . "</td>" .
		"	<td width='108' align='right'>" . $totTre . "</td>" .
		"	<td width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		/**
		 * Totale complessivo di colonna
		 */
		
		$totBre = ($totale_Bre != 0) ? number_format(abs($totale_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totale_Tre != 0) ? number_format(abs($totale_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totale_Vil != 0) ? number_format(abs($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totale_Bre + $totale_Tre + $totale_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_ricavi .=
		"<tr>" .
		"	<td class='enlarge' width='308' align='left'>%ml.totale% %ml.ricavi%</td>" .
		"	<td class='mark' width='108' align='right'>" . $totBre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totTre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		$_SESSION['numRicaviTrovati'] = $numReg;
		$risultato_ricavi = $risultato_ricavi . "</tbody>";
		
		$risultato_ricavi = $risultato_ricavi . "</table></div>";
		
		/**
		 * Metto in sessione il totale ricavi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
		 */
		
		$_SESSION['totaleRicavi_Bre'] = abs($totale_Bre);
		$_SESSION['totaleRicavi_Tre'] = abs($totale_Tre);
		$_SESSION['totaleRicavi_Vil'] = abs($totale_Vil);
		$_SESSION['totaleRicavi'] = abs($totale);
				
		return $risultato_ricavi;
	}

	/**
	 * Questo metodo costruisce una tabella html delle attività comparate
	 * @param unknown $array
	 * @param unknown $dati
	 */
	public function makeTableAttivoComparati($array, $dati) {
		
		$risultato_attivo =
		"<table class='result'>" .
		"	<thead>" .
		"		<th width='300'>%ml.desconto%</th>" .
		"		<th width='100'>%ml.brembate%</th>" .
		"		<th width='100'>%ml.trezzo%</th>" .
		"		<th width='100'>%ml.villa%</th>" .
		"		<th width='100'>%ml.totale%</th>" .
		"	</thead>" .
		"</table>" .
		"<div class='scroll-riepilogo-negozi'>" .
		"	<table class='result'>" .
		"		<tbody>";
	
		$numReg = 0;
		$totaleAttivo = 0;
		$desconto_break = "";
		
		$totaleConto_Bre = 0;
		$totaleConto_Tre = 0;
		$totaleConto_Vil = 0;
		
		$totale_Bre = 0;
		$totale_Tre = 0;
		$totale_Vil = 0;
	
		foreach(pg_fetch_all($dati) as $row) {
			
			$totaleConto = trim($row['tot_conto']);
			$totaleAttivo += $totaleConto;
			
			if (trim($row['cod_negozio']) == "BRE") $totale_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totale_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totale_Vil += $totaleConto;
				
			$numReg ++;
					
			if (trim($row['des_conto']) != $desconto_break ) {
	
				if ($desconto_break != "") {

					$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
						
					$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
					$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					
					$risultato_attivo .=
					"<tr>" .
					"	<td width='308' align='left'>" . $desconto_break . "</td>" .
					"	<td width='108' align='right'>" . $totBre . "</td>" .
					"	<td width='108' align='right'>" . $totTre . "</td>" .
					"	<td width='108' align='right'>" . $totVil . "</td>" .
					"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
					"</tr>";
					
					$totaleConto_Bre = 0;
					$totaleConto_Tre = 0;
					$totaleConto_Vil = 0;						
				}
				
				$desconto_break = trim($row['des_conto']);
			}
				
			if (trim($row['cod_negozio']) == "BRE") $totaleConto_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totaleConto_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totaleConto_Vil += $totaleConto;
		}

		$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_attivo .=
		"<tr>" .
		"	<td width='308' align='left'>" . $desconto_break . "</td>" .
		"	<td width='108' align='right'>" . $totBre . "</td>" .
		"	<td width='108' align='right'>" . $totTre . "</td>" .
		"	<td width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		/**
		 * Totale complessivo di colonna
		 */
		
		$totBre = ($totale_Bre != 0) ? number_format(abs($totale_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totale_Tre != 0) ? number_format(abs($totale_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totale_Vil != 0) ? number_format(abs($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totale_Bre + $totale_Tre + $totale_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_attivo .=
		"<tr>" .
		"	<td class='enlarge' width='308' align='left'>%ml.totale% %ml.attivo%</td>" .
		"	<td class='mark' width='108' align='right'>" . $totBre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totTre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		$_SESSION['numAttiviTrovati'] = $numReg;
		$risultato_attivo = $risultato_attivo . "</tbody>";
		
		$risultato_attivo = $risultato_attivo . "</table></div>";
		
		/**
		 * Metto in sessione i totali attivi 
		 */
		
		$_SESSION['totaleAttivo_Bre'] = abs($totale_Bre);
		$_SESSION['totaleAttivo_Tre'] = abs($totale_Tre);
		$_SESSION['totaleAttivo_Vil'] = abs($totale_Vil);
		$_SESSION['totaleAttivo'] = abs($totale);
		
		return $risultato_attivo;
	}
	
	/**
	 * Questo metodo costruisce una tabella html delle passività comparate
	 * @param unknown $array
	 * @param unknown $dati
	 */
	public function makeTablePassivoComparati($array, $dati) {

		$risultato_passivo =
		"<table class='result'>" .
		"	<thead>" .
		"		<th width='300'>%ml.desconto%</th>" .
		"		<th width='100'>%ml.brembate%</th>" .
		"		<th width='100'>%ml.trezzo%</th>" .
		"		<th width='100'>%ml.villa%</th>" .
		"		<th width='100'>%ml.totale%</th>" .
		"	</thead>" .
		"</table>" .
		"<div class='scroll-riepilogo-negozi'>" .
		"	<table class='result'>" .
		"		<tbody>";
	
		$numReg = 0;
		$totalePassivo = 0;
		$desconto_break = "";

		$totaleConto_Bre = 0;
		$totaleConto_Tre = 0;
		$totaleConto_Vil = 0;
		
		$totale_Bre = 0;
		$totale_Tre = 0;
		$totale_Vil = 0;
		
		foreach(pg_fetch_all($dati) as $row) {
				
			$totaleConto = trim($row['tot_conto']);
			$totalePassivo += $totaleConto;

			if (trim($row['cod_negozio']) == "BRE") $totale_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totale_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totale_Vil += $totaleConto;
			
			$numReg ++;
					
			if (trim($row['des_conto']) != $desconto_break ) {
	
				if ($desconto_break != "") {
					
					$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					
					$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
					$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
						
					$risultato_passivo .=
					"<tr>" .
					"	<td width='308' align='left'>" . $desconto_break . "</td>" .
					"	<td width='108' align='right'>" . $totBre . "</td>" .
					"	<td width='108' align='right'>" . $totTre . "</td>" .
					"	<td width='108' align='right'>" . $totVil . "</td>" .
					"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
					"</tr>";
						
					$totaleConto_Bre = 0;
					$totaleConto_Tre = 0;
					$totaleConto_Vil = 0;
				}
				
				$desconto_break = trim($row['des_conto']);
			}
					
			if (trim($row['cod_negozio']) == "BRE") $totaleConto_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totaleConto_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totaleConto_Vil += $totaleConto;
		}

		$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_passivo .=
		"<tr>" .
		"	<td width='308' align='left'>" . $desconto_break . "</td>" .
		"	<td width='108' align='right'>" . $totBre . "</td>" .
		"	<td width='108' align='right'>" . $totTre . "</td>" .
		"	<td width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		/**
		 * Totale complessivo di colonna
		 */
		
		$totBre = ($totale_Bre != 0) ? number_format(abs($totale_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totale_Tre != 0) ? number_format(abs($totale_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totale_Vil != 0) ? number_format(abs($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totale_Bre + $totale_Tre + $totale_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$risultato_passivo .=
		"<tr>" .
		"	<td class='enlarge' width='308' align='left'>%ml.totale% %ml.passivo%</td>" .
		"	<td class='mark' width='108' align='right'>" . $totBre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totTre . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $totVil . "</td>" .
		"	<td class='mark' width='108' align='right'>" . $tot . "</td>" .
		"</tr>";
		
		$_SESSION['numAttiviTrovati'] = $numReg;
		$risultato_passivo = $risultato_passivo . "</tbody>";
		
		$risultato_passivo = $risultato_passivo . "</table></div>";
		
		/**
		 * Metto in sessione i totali attivi
		 */
		
		$_SESSION['totalePassivo_Bre'] = abs($totale_Bre);
		$_SESSION['totalePassivo_Tre'] = abs($totale_Tre);
		$_SESSION['totalePassivo_Vil'] = abs($totale_Vil);
		$_SESSION['totalePassivo'] = abs($totale);
		
		return $risultato_passivo;
	}
	
	/**
	 * Questo metodo costruisce una tabella html per i risultati del calcolo dell' MCT
	 * @param unknown $costoVariabile
	 * @param unknown $ricavoVendita
	 * @param unknown $costoFisso
	 */
	public function makeTableMargineContribuzione() {
			
		$margineContribuzione = "";
		$totaleCostiVariabili = 0;
		$totaleRicavi = 0;
		$totaleCostiFissi = 0;
		$margineTotale = 0;
		$marginePercentuale = 0;
		$ricaricoPercentuale = 0;
		
		// Villa ---------------------------------------------------------------------

		$totaleCostiVariabiliVIL = 0;
		$totaleRicaviVIL = 0;
		$totaleCostiFissiVIL = 0;
		$margineTotaleVIL = 0;
		$marginePercentualeVIL = 0;
		$ricaricoPercentualeVIL = 0;
		
		foreach(pg_fetch_all($_SESSION['costoVariabileVIL']) as $row) {
			$totaleCostiVariabiliVIL = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiVIL']) as $row) {
			$totaleRicaviVIL = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoVIL']) as $row) {
			$totaleCostiFissiVIL = trim($row['totalecostofisso']);
		}
		
		$margineTotaleVIL = abs($totaleRicaviVIL) - $totaleCostiVariabiliVIL;
		$marginePercentualeVIL = ($margineTotaleVIL * 100 ) / abs($totaleRicaviVIL);
		$ricaricoPercentualeVIL = ($margineTotaleVIL * 100) / abs($totaleCostiVariabiliVIL);

		$totaleRicavi += abs($totaleRicaviVIL);
		$totaleCostiVariabili += $totaleCostiVariabiliVIL;	
		
		// Trezzo ---------------------------------------------------------------------

		$totaleCostiVariabiliTRE = 0;
		$totaleRicaviTRE = 0;
		$totaleCostiFissiTRE = 0;
		$margineTotaleTRE = 0;
		$marginePercentualeTRE = 0;
		$ricaricoPercentualeTRE = 0;
		
		foreach(pg_fetch_all($_SESSION['costoVariabileTRE']) as $row) {
			$totaleCostiVariabiliTRE = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiTRE']) as $row) {
			$totaleRicaviTRE = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoTRE']) as $row) {
			$totaleCostiFissiTRE = trim($row['totalecostofisso']);
		}
		
		$margineTotaleTRE = abs($totaleRicaviTRE) - $totaleCostiVariabiliTRE;
		$marginePercentualeTRE = ($margineTotaleTRE * 100 ) / abs($totaleRicaviTRE);
		$ricaricoPercentualeTRE = ($margineTotaleTRE * 100) / abs($totaleCostiVariabiliTRE);

		$totaleRicavi += abs($totaleRicaviTRE);
		$totaleCostiVariabili += $totaleCostiVariabiliTRE;
		
		// Brembate ---------------------------------------------------------------------

		$totaleCostiVariabiliBRE = 0;
		$totaleRicaviBRE = 0;
		$totaleCostiFissiBRE = 0;
		$margineTotaleBRE = 0;
		$marginePercentualeBRE = 0;
		$ricaricoPercentualeBRE = 0;
		
		foreach(pg_fetch_all($_SESSION['costoVariabileBRE']) as $row) {
			$totaleCostiVariabiliBRE = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiBRE']) as $row) {
			$totaleRicaviBRE = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoBRE']) as $row) {
			$totaleCostiFissiBRE = trim($row['totalecostofisso']);
		}
		
		$margineTotaleBRE = abs($totaleRicaviBRE) - $totaleCostiVariabiliBRE;
		$marginePercentualeBRE = ($margineTotaleBRE * 100 ) / abs($totaleRicaviBRE);
		$ricaricoPercentualeBRE = ($margineTotaleBRE * 100) / abs($totaleCostiVariabiliBRE);

		$totaleRicavi += abs($totaleRicaviBRE);
		$totaleCostiVariabili += $totaleCostiVariabiliBRE;

		// MCT totale negozi ---------------------------------------------------------------------

		$margineTotale = abs($totaleRicavi) - $totaleCostiVariabili;
		$marginePercentuale = ($margineTotale * 100 ) / abs($totaleRicavi);
		$ricaricoPercentuale = ($margineTotale * 100) / abs($totaleCostiVariabili);
		
		/**
		 * Creo la tabella 
		 */
		
		$margineContribuzione =
		"<table class='result'>" .
		"	<thead>" .
		"		<th width='300'>&nbsp;</th>" .
		"		<th width='100'>%ml.brembate%</th>" .
		"		<th width='100'>%ml.trezzo%</th>" .
		"		<th width='100'>%ml.villa%</th>" .
		"		<th width='100'>%ml.totale%</th>" .
		"	</thead>" .
		"	<tbody>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>%ml.fatturato%</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicaviBRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicaviTRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicaviVIL), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>%ml.acquisti%</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabiliBRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabiliTRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabiliVIL), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabili), 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>%ml.margineAssoluto%</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($margineTotaleBRE, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($margineTotaleTRE, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($margineTotaleVIL, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($margineTotale, 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>%ml.marginePercentuale%</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($marginePercentualeBRE, 2, ',', '.') . " &#37;</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($marginePercentualeTRE, 2, ',', '.') . " &#37;</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($marginePercentualeVIL, 2, ',', '.') . " &#37;</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($marginePercentuale, 2, ',', '.') . " &#37;</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>%ml.ricaricoPercentuale%</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($ricaricoPercentualeBRE, 2, ',', '.') . " &#37;</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($ricaricoPercentualeTRE, 2, ',', '.') . " &#37;</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($ricaricoPercentualeVIL, 2, ',', '.') . " &#37;</td>" .
		"			<td width='108' align='right' class='mark'>" . number_format($ricaricoPercentuale, 2, ',', '.') . " &#37;</td>" .
		"		</tr>" .
		"   </tbody>" .
		"</table>" ;
		
		return $margineContribuzione;		
	}
	
	/**
	 * Questo metodo costruisce una tabella html per i risultati del calcolo del BEP
	 * @param unknown $costoVariabile
	 * @param unknown $ricavoVenditaProdotti
	 * @param unknown $costoFisso
	 */
	public function makeTableBep() {

		/**
		 * Calcolo del Break Eaven Point
		 *
		 * Il calcolo del BEP per un’azienda che realizza prodotti
		 * si ottiene imponendo l’eguaglianza fra il fatturato totale e i costi totali ovvero
		 *
		 *             Fatturato totale = Costi totali
		 *
		 * Metodo analitico: scrivendo le formule1 che esprimono i costi totali ed i ricavi,
		 * con qualche passaggio matematico è possibile determinare che si intersecano se:
		 *
		 *              BEP = CF / (1 – (CV / FAT))
		 *
		 * Dove:
		 *
		 * FAT è il fatturato
		 * CF sono i costi fissi
		 * CV sono i costi variabili e quindi CV/FAT è l’incidenza dei costi variabili sul fatturato
		 * CT sono i costi totali e quindi CT = CF + CV
		 *
		 */
		
		$tabellaBep = "";

		$totaleCostiVariabili = 0;
		$totaleRicavi = 0;
		$totaleCostiFissi = 0;
		$incidenzaCostiVariabiliSulFatturato = 0;
		$bep = 0;
		
		// Villa ---------------------------------------------------------------------
		
		$totaleCostiVariabiliVIL = 0;
		$totaleRicaviVIL = 0;
		$totaleCostiFissiVIL = 0;
		$incidenzaCostiVariabiliSulFatturatoVIL = 0;
		$bepVIL = 0;		
		
		foreach(pg_fetch_all($_SESSION['costoVariabileVIL']) as $row) {
			$totaleCostiVariabiliVIL = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiVIL']) as $row) {
			$totaleRicaviVIL = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoVIL']) as $row) {
			$totaleCostiFissiVIL = trim($row['totalecostofisso']);
		}
		
		$incidenzaCostiVariabiliSulFatturatoVIL = 1 - ($totaleCostiVariabiliVIL / abs($totaleRicaviVIL));
		$bepVIL = $totaleCostiFissiVIL / round($incidenzaCostiVariabiliSulFatturatoVIL, 2);

		$totaleCostiVariabili += $totaleCostiVariabiliVIL;
		$totaleRicavi += $totaleRicaviVIL;
		$totaleCostiFissi += $totaleCostiFissiVIL;		
		
		// Trezzo ---------------------------------------------------------------------

		$totaleCostiVariabiliTRE = 0;
		$totaleRicaviTRE = 0;
		$totaleCostiFissiTRE = 0;
		$incidenzaCostiVariabiliSulFatturatoTRE = 0;
		$bepTRE = 0;
		
		foreach(pg_fetch_all($_SESSION['costoVariabileTRE']) as $row) {
			$totaleCostiVariabiliTRE = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiTRE']) as $row) {
			$totaleRicaviTRE = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoTRE']) as $row) {
			$totaleCostiFissiTRE = trim($row['totalecostofisso']);
		}
		
		$incidenzaCostiVariabiliSulFatturatoTRE = 1 - ($totaleCostiVariabiliTRE / abs($totaleRicaviTRE));
		$bepTRE = $totaleCostiFissiTRE / round($incidenzaCostiVariabiliSulFatturatoTRE, 2);

		$totaleCostiVariabili += $totaleCostiVariabiliTRE;
		$totaleRicavi += $totaleRicaviTRE;
		$totaleCostiFissi += $totaleCostiFissiTRE;
		
		// Brembate ---------------------------------------------------------------------

		$totaleCostiVariabiliBRE = 0;
		$totaleRicaviBRE = 0;
		$totaleCostiFissiBRE = 0;
		$incidenzaCostiVariabiliSulFatturatoBRE = 0;
		$bepBRE = 0;
		
		foreach(pg_fetch_all($_SESSION['costoVariabileBRE']) as $row) {
			$totaleCostiVariabiliBRE = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiBRE']) as $row) {
			$totaleRicaviBRE = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoBRE']) as $row) {
			$totaleCostiFissiBRE = trim($row['totalecostofisso']);
		}
		
		$incidenzaCostiVariabiliSulFatturatoBRE = 1 - ($totaleCostiVariabiliBRE / abs($totaleRicaviBRE));
		$bepBRE = $totaleCostiFissiBRE / round($incidenzaCostiVariabiliSulFatturatoBRE, 2);

		$totaleCostiVariabili += $totaleCostiVariabiliBRE;
		$totaleRicavi += $totaleRicaviBRE;
		$totaleCostiFissi += $totaleCostiFissiBRE;
		
		// BEP totale negozi -----------------------------------------------------
		
		$incidenzaCostiVariabiliSulFatturato = 1 - ($totaleCostiVariabili / abs($totaleRicavi));
		$bep = $totaleCostiFissi / round($incidenzaCostiVariabiliSulFatturato, 2);
		
		/**
		 * tabella del BEP
		 */
		
		$tabellaBep =
		"<table class='result'>" .
		"	<thead>" .
		"		<th width='300'>&nbsp;</th>" .
		"		<th width='100'>%ml.brembate%</th>" .
		"		<th width='100'>%ml.trezzo%</th>" .
		"		<th width='100'>%ml.villa%</th>" .
		"		<th width='100'>%ml.totale%</th>" .
		"	</thead>" .
		"	<tbody>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>Fatturato</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicaviBRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicaviTRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicaviVIL), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>Costi fissi</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiFissiBRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiFissiTRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiFissiVIL), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiFissi), 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>Acquisti</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabiliBRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabiliTRE), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabiliVIL), 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabili), 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>Incidenza acquisti sul fatturato</td>" .
		"			<td width='108' align='right' class='mark'> " . number_format($incidenzaCostiVariabiliSulFatturatoBRE, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'> " . number_format($incidenzaCostiVariabiliSulFatturatoTRE, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'> " . number_format($incidenzaCostiVariabiliSulFatturatoVIL, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'> " . number_format($incidenzaCostiVariabiliSulFatturato, 2, ',', '.') . "</td>" .
		"		</tr>" .
		"		<tr height='30'>" .
		"			<td width='308' align='left' class='enlarge'>BEP</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($bepBRE, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($bepTRE, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($bepVIL, 2, ',', '.') . "</td>" .
		"			<td width='108' align='right' class='mark'>&euro; " . number_format($bep, 2, ',', '.') . "</td>" .
		"		</tr>" .
		"   </tbody>" .
		"</table>" ;
		
		return $tabellaBep;		
	}
	/**
	 * Questo metodo preleva i costi variabili di ciascun negozio
	 * @param unknown $utility
	 * @param unknown $db
	 */
	public function ricercaCostiVariabiliNegozi($utility, $db) {
	
		// Villa -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'VIL'"
		);
	
		if ($this->ricercaCostiMargineContribuzione($utility, $db, $replace)) {
			if (isset($_SESSION['costoVariabile'])) {
				$_SESSION['costoVariabileVIL'] = $_SESSION['costoVariabile'];
			}
		}
	
		// Trezzo ----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'TRE'"
		);
	
		if ($this->ricercaCostiMargineContribuzione($utility, $db, $replace)) {
			if (isset($_SESSION['costoVariabile'])) {
				$_SESSION['costoVariabileTRE'] = $_SESSION['costoVariabile'];
			}
		}
	
		// Brembate -------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'BRE'"
		);
	
		if ($this->ricercaCostiMargineContribuzione($utility, $db, $replace)) {
			if (isset($_SESSION['costoVariabile'])) {
				$_SESSION['costoVariabileBRE'] = $_SESSION['costoVariabile'];
			}
		}
	}
	
	/**
	 * Questo metodo preleva i costi fissi di ciascun negozio
	 * @param unknown $utility
	 * @param unknown $db
	 */
	public function ricercaCostiFissiNegozi($utility, $db) {
	
		// Villa -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'VIL'"
		);
	
		if ($this->ricercaCostiFissi($utility, $db, $replace)) {
			if (isset($_SESSION['costoFisso'])) {
				$_SESSION['costoFissoVIL'] = $_SESSION['costoFisso'];
			}
		}
	
		// Trezzo -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'TRE'"
		);
	
		if ($this->ricercaCostiFissi($utility, $db, $replace)) {
			if (isset($_SESSION['costoFisso'])) {
				$_SESSION['costoFissoTRE'] = $_SESSION['costoFisso'];
			}
		}
	
		// Brembate -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'BRE'"
		);
	
		if ($this->ricercaCostiFissi($utility, $db, $replace)) {
			if (isset($_SESSION['costoFisso'])) {
				$_SESSION['costoFissoBRE'] = $_SESSION['costoFisso'];
			}
		}
	}
	
	/**
	 * Questo metodo preleva i ricavi di ciascun negozio
	 * @param unknown $utility
	 * @param unknown $db
	 */
	public function ricercaRicaviFissiNegozi($utility, $db) {
	
		// Villa -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'VIL'"
		);
	
		if ($this->ricercaRicaviMargineContribuzione($utility, $db, $replace)) {
			if (isset($_SESSION['ricavoVenditaProdotti'])) {
				$_SESSION['ricavoVenditaProdottiVIL'] = $_SESSION['ricavoVenditaProdotti'];
			}
		}
	
		// Trezzo -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'TRE'"
		);
	
		if ($this->ricercaRicaviMargineContribuzione($utility, $db, $replace)) {
			if (isset($_SESSION['ricavoVenditaProdotti'])) {
				$_SESSION['ricavoVenditaProdottiTRE'] = $_SESSION['ricavoVenditaProdotti'];
			}
		}
	
		// Brembate -----------------------------------------------------
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => "'BRE'"
		);
	
		if ($this->ricercaRicaviMargineContribuzione($utility, $db, $replace)) {
			if (isset($_SESSION['ricavoVenditaProdotti'])) {
				$_SESSION['ricavoVenditaProdottiBRE'] = $_SESSION['ricavoVenditaProdotti'];
			}
		}
	}	
}		

?>
		
	