<?php

require_once 'scadenze.abstract.class.php';

class RicercaScadenzeTemplate extends ScadenzeAbstract {

	private static $_instance = null;

	private static $pagina = "/scadenze/ricercaScadenze.form.html";

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

			self::$_instance = new RicercaScadenzeTemplate();

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
		
		if ($_SESSION["datascad_da"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di inizio ricerca";
			$esito = FALSE;
		}
		
		if ($_SESSION["datascad_a"] == "") {
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
		
		if (isset($_SESSION["scadenzeTrovate"])) {
		
			$risultato_ricerca =
			"<table id='scadenze' class='display'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th>%ml.datscadenza%</th>" .
			"			<th>%ml.codforn%</th>" .
			"			<th>%ml.notascadenza%</th>" .
			"			<th>%ml.numfatt%</th>" .
			"			<th>%ml.tipaddebito%</th>" .
			"			<th>%ml.stascadenza%</th>" .
			"			<th>%ml.impscadenza%</th>" .
			"			<th></th>" .
			"			<th></th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";
		
			$scadenzeTrovate = $_SESSION["scadenzeTrovate"];
			$numScadenze = 0;
		
			$idfornitore_break = ""; 
			$datscadenza_break = "";
			$totale_fornitore = 0;
			$totale_scadenze = 0;
			
			foreach(pg_fetch_all($scadenzeTrovate) as $row) {
		
				if (($idfornitore_break == "") && ($datscadenza_break == "")) {
					$idfornitore_break = trim($row['id_fornitore']);
					$datscadenza_break = trim($row['dat_scadenza']);
					$desfornitore = trim($row['des_fornitore']);
					$datscadenza  = trim($row['dat_scadenza']);
					$desfornitore2 = trim($row['des_fornitore']);
					$datscadenza2  = trim($row['dat_scadenza_yyyymmdd']);
				}

				if (trim($row['sta_registrazione']) == "00") {
					$class = "class=''";
					$bottoneModificaRegistrazione = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modificaFattura%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
				}
				else {
					$class = "class=''";
					$bottoneModificaRegistrazione = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizzaFattura%'><span class='ui-icon ui-icon-search'></span></li></a>";
				}				
				
				if (trim($row['nota_scadenza']) != "") {$notascadenza = trim($row['nota_scadenza']);}
				else {$notascadenza = "&ndash;&ndash;&ndash;";} 

				if (trim($row['tip_addebito']) != "") {$tipaddebito = trim($row['tip_addebito']);}
				else {$tipaddebito = "&ndash;&ndash;&ndash;";}

				if (trim($row['sta_scadenza']) == "  ") {
					$stascadenza = "&ndash;&ndash;&ndash;";
					$tdclass = "class='dt-ko'";
					$bottoneModificaPagamento = "";
				}
				
				if (trim($row['sta_scadenza']) == "00") {
					$stascadenza = "Da Pagare";
					$tdclass = "class='dt-ko'";
					$bottoneModificaPagamento = "";
				}
				
				if (trim($row['sta_scadenza']) == "10") {
					$stascadenza = "Pagato";
					$tdclass = "class='dt-ok'";
					$bottoneModificaPagamento = "<a class='tooltip' href='../primanota/modificaPagamentoFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "&idPagamento= " . trim($row['id_pagamento']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizzaPagamento%'><span class='ui-icon ui-icon-link'></span></li></a>";
				}

				if (trim($row['sta_scadenza']) == "02") {
					$stascadenza = "Posticipato";
					$tdclass = "class='dt-chiuso'";
					$bottoneModificaPagamento = "<a class='tooltip' href='../primanota/modificaPagamentoFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "&idPagamento= " . trim($row['id_pagamento']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizzaPagamento%'><span class='ui-icon ui-icon-link'></span></li></a>";
				}
				
				$numfatt = trim($row['num_fattura']);
				
				$numScadenze ++;
				
				if ((trim($row['id_fornitore']) != $idfornitore_break) | (trim($row['dat_scadenza']) != $datscadenza_break)) {
					
					$risultato_ricerca = $risultato_ricerca .
					"<tr class='dt-subtotale'>" .
					"	<td class='dt-center'>" . $datscadenza2 . "</td>" .
					"	<td>" . $desfornitore2 . "</td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td class='dt-right'><i>Totale data</i></td>" .
					"	<td></td>" .
					"	<td class='dt-right'>" . number_format($totale_fornitore, 2, ',', '.') . "</td>" .
					"	<td id='icons'></td>" .
					"	<td id='icons'></td>" .
					"</tr>";
					
					$desfornitore = trim($row['des_fornitore']);
					$datscadenza  = trim($row['dat_scadenza']);
					$desfornitore2 = trim($row['des_fornitore']);
					$datscadenza2  = trim($row['dat_scadenza_yyyymmdd']);
					
					$idfornitore_break = trim($row['id_fornitore']);
					$datscadenza_break = trim($row['dat_scadenza']);
						
					$totale_scadenze += $totale_fornitore;  
					$totale_fornitore = 0;
				}
					
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td class='dt-center'>" . $datscadenza2 . "</td>" .
				"	<td>" . $desfornitore2 . "</td>" .
				"	<td class='dt-center'>" . $datscadenza . "</td>" .
				"	<td>" . $desfornitore . "</td>" .
				"	<td>" . $notascadenza . "</td>" .
				"	<td class='dt-center'>" . $numfatt . "</td>" .
				"	<td class='dt-center'>" . $tipaddebito . "</td>" .
				"	<td " . $tdclass . ">" . $stascadenza . "</td>" .
				"	<td class='dt-right'>" . number_format(trim($row['imp_in_scadenza']), 2, ',', '.') . "</td>" .
				"	<td id='icons'>" . $bottoneModificaRegistrazione . "</td>" .
				"	<td id='icons'>" . $bottoneModificaPagamento . "</td>" .
				"</tr>";

				$desfornitore = "";
				$datscadenza = "";
				$totale_fornitore += trim($row['imp_in_scadenza']);						
			}
			
			$risultato_ricerca = $risultato_ricerca .
			"<tr class='dt-subtotale'>" .
			"	<td class='dt-center'>" . $datscadenza2 . "</td>" .
			"	<td>" . $desfornitore2 . "</td>" .
			"	<td class='dt-center'>" . $datscadenza . "</td>" .
			"	<td>" . $desfornitore . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='dt-right'><i>Totale fornitore</i></td>" .
			"	<td></td>" .
			"	<td class='dt-right'>" . number_format($totale_fornitore, 2, ',', '.') . "</td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"</tr>";

			$totale_scadenze += $totale_fornitore;

			$risultato_ricerca = $risultato_ricerca .
			"<tr class='dt-totale'>" .
			"	<td class='dt-center'>31/12/9999</td>" .
			"	<td></td>" .
			"	<td class='dt-center'>" . $datscadenza . "</td>" .
			"	<td>" . $desfornitore . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='dt-right'><i>Totale scadenze</i></td>" .
			"	<td></td>" .
			"	<td class='dt-right'>" . number_format($totale_scadenze, 2, ',', '.') . "</td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"</tr>";
			
			$_SESSION['numScadenzeTrovate'] = $numScadenze;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";
		}
		else {
		
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%datascad_da%' => $_SESSION["datascad_da"],
				'%datascad_a%' => $_SESSION["datascad_a"],
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