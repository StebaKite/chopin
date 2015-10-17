<?php

require_once 'scadenze.abstract.class.php';

class RicercaScadenzeClienteTemplate extends ScadenzeAbstract {

	private static $_instance = null;

	private static $pagina = "/scadenze/ricercaScadenzeCliente.form.html";

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

			self::$_instance = new RicercaScadenzeClienteTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

		$esito = TRUE;
		$msg = "<br>";
		
		/**
		 * Controllo presenza dati obbligatori
		 */
		
		if ($_SESSION["datareg_da"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di inizio ricerca";
			$esito = FALSE;
		}
		
		if ($_SESSION["datareg_a"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di fine ricerca";
			$esito = FALSE;
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
		$risultato_ricerca = "";
		
		if (isset($_SESSION["scadenzeClienteTrovate"])) {
		
			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='70'>%ml.datregistrazione%</th>" .
			"		<th width='200'>%ml.codcliente%</th>" .
			"		<th width='250'>%ml.notascadenza%</th>" .
			"		<th width='50'>%ml.numfatt%</th>" .
			"		<th width='90'>%ml.tipaddebito%</th>" .
			"		<th width='80'>%ml.stascadenza%</th>" .
			"		<th width='90'>%ml.impscadenza%</th>" .
			"		<th width='52' colspan='2'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-scadenze'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$scadenzeClienteTrovate = $_SESSION["scadenzeClienteTrovate"];
			$numScadenze = 0;
		
			$idcliente_break = ""; 
			$datregistrazione_break = "";
			$totale_cliente = 0;
			$totale_scadenze = 0;
			
			foreach(pg_fetch_all($scadenzeClienteTrovate) as $row) {
		
				if (($idcliente_break == "") && ($datregistrazione_break == "")) {
					$idcliente_break = trim($row['id_cliente']);
					$datregistrazione_break = trim($row['dat_registrazione']);
					$descliente = trim($row['des_cliente']);
					$datregistrazione  = trim($row['dat_registrazione']);
				}
				
				if (trim($row['sta_registrazione']) == "00") {
					$class = "class=''";
					$bottoneModificaRegistrazione = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modificaFattura%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
				}
				else {
					$class = "class=''";
					$bottoneModificaRegistrazione = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizzaFattura%'><span class='ui-icon ui-icon-search'></span></li></a>";
				}
				
				if (trim($row['nota']) != "") {$nota = trim($row['nota']);}
				else {$nota = "&ndash;&ndash;&ndash;";} 

				if (trim($row['tip_addebito']) != "") {$tipaddebito = trim($row['tip_addebito']);}
				else {$tipaddebito = "&ndash;&ndash;&ndash;";}

				if (trim($row['sta_scadenza']) == "00") {
					$stascadenza = "Da Incassare";
					$tdclass = "class='ko'";
					$bottoneModificaIncasso = "";
				}
				
				if (trim($row['sta_scadenza']) == "10") {
					$stascadenza = "Incassato";
					$tdclass = "class='ok'";
					$bottoneModificaIncasso = "<a class='tooltip' href='../primanota/modificaIncassoFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "&idIncasso=" . trim($row['id_incasso']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modificaIncasso%'><span class='ui-icon ui-icon-link'></span></li></a>";
				}

				if (trim($row['sta_scadenza']) == "02") {
					$stascadenza = "Posticipato";
					$tdclass = "class='mark'";
					$bottoneModificaIncasso = "<a class='tooltip' href='../primanota/modificaIncassoFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "&idIncasso=" . trim($row['id_incasso']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modificaIncasso%'><span class='ui-icon ui-icon-link'></span></li></a>";
				}
				
				$numfatt = trim($row['num_fattura']);
				
				$numScadenze ++;
				
				if ((trim($row['id_cliente']) != $idcliente_break) | (trim($row['dat_registrazione']) != $datregistrazione_break)) {
					
					$risultato_ricerca = $risultato_ricerca .
					"<tr class='subtotale'>" .
					"	<td colspan='5' align='right'><i>Totale</i></td>" .
					"	<td colspan='2' align='right'>&euro;" . number_format($totale_fornitore, 2, ',', '.') . "</td>" .
					"	<td width='45' id='icons' colspan='2'>&nbsp;</td>" .
					"</tr>";
					
					$descliente = trim($row['des_cliente']);
					$datregistrazione  = trim($row['dat_registrazione']);
					$idcliente_break = trim($row['id_cliente']);
					$datregistrazione_break = trim($row['dat_registrazione']);
						
					$totale_scadenze += $totale_cliente;  
					$totale_cliente = 0;
				}
					
				$risultato_ricerca = $risultato_ricerca .
				"<tr " . $class . " id='" . trim($row['id_scadenza']) . "'>" .
				"	<td width='78' align='center'>" . $datregistrazione . "</td>" .
				"	<td width='208' align='left'>" . $descliente . "</td>" .
				"	<td width='258' align='left'>" . $nota . "</td>" .
				"	<td width='58' align='center'>" . $numfatt . "</td>" .
				"	<td width='98' align='center'>" . $tipaddebito . "</td>" .
				"	<td width='88' align='center'" . $tdclass . ">" . $stascadenza . "</td>" .
				"	<td width='98' align='right'>&euro;" . number_format(trim($row['imp_registrazione']), 2, ',', '.') . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneModificaRegistrazione . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneModificaIncasso . "</td>" .
				"</tr>";

				$descliente = "";
				$datregistrazione = "";
				$totale_cliente += trim($row['imp_registrazione']);						
			}
			
			$risultato_ricerca = $risultato_ricerca .
			"<tr class='subtotale'>" .
			"	<td colspan='5' align='right'><i>Totale</i></td>" .
			"	<td colspan='2' align='right'>&euro;" . number_format($totale_cliente, 2, ',', '.') . "</td>" .
			"	<td width='45' id='icons' colspan='2'>&nbsp;</td>" .
			"</tr>";				

			$totale_scadenze += $totale_cliente;

			$risultato_ricerca = $risultato_ricerca .
			"<tr class='totale'>" .
			"	<td class='mark' colspan='5' align='right'>Totale Scadenze</td>" .
			"	<td class='mark' colspan='2' align='right'>&euro;" . number_format($totale_scadenze, 2, ',', '.') . "</td>" .
			"	<td width='45' id='icons' colspan='2'>&nbsp;</td>" .
			"</tr>";
			
			$_SESSION['numScadenzeClienteTrovate'] = $numScadenze;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {
		
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%00-selected%' => ($_SESSION["statoscad_sel"] == "00") ? "selected" : "",
				'%10-selected%' => ($_SESSION["statoscad_sel"] == "10") ? "selected" : "",
				'%02-selected%' => ($_SESSION["statoscad_sel"] == "02") ? "selected" : "",
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf'],
				'%risultato_ricerca%' => $risultato_ricerca
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		}
	}

?>