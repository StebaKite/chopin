<?php

require_once 'anagrafica.abstract.class.php';

class RicercaFornitoreTemplate extends AnagraficaAbstract {

	private static $_instance = null;

	private static $pagina = "/anagrafica/ricercaFornitore.form.html";

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

			self::$_instance = new RicercaFornitoreTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$form = self::$root . $array['template'] . self::$pagina;
		$risultato_ricerca = "";
		
		if (isset($_SESSION["fornitoriTrovati"])) {
		
			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='70'>%ml.codfornitore%</th>" .
			"		<th width='200'>%ml.desfornitore%</th>" .
			"		<th width='200'>%ml.desindirizzofornitore%</th>" .
			"		<th width='150'>%ml.descittafornitore%</th>" .
			"		<th width='50'>%ml.capfornitore%</th>" .
			"		<th width='50'>%ml.tipaddebito%</th>" .
			"		<th width='47'>%ml.qtareg%</th>" .
			"		<th width='52' colspan='2'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-fornitori'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$fornitoriTrovati = $_SESSION["fornitoriTrovati"];
			$numFornitori = 0;
		
			foreach(pg_fetch_all($fornitoriTrovati) as $row) {
		
				if ($row['tot_registrazioni_fornitore'] == 0) {
					$class = "class=''";
				}
				else {
					$class = "class=''";
				}
		
				if ($row['tot_registrazioni_fornitore'] == 0) {
					$bottoneModifica = "<a class='tooltip' href='../anagrafica/modificaFornitoreFacade.class.php?modo=start&idfornitore=" . trim($row['id_fornitore']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "<a class='tooltip' onclick='cancellaFornitore(" . trim($row['id_fornitore']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$bottoneModifica = "<a class='tooltip' href='../anagrafica/modificaFornitoreFacade.class.php?modo=start&idfornitore=" . trim($row['id_fornitore']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}
		
				$numFornitori ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr " . $class . " id='" . trim($row['id_fornitore']) . "'>" .
				"	<td width='78' class='tooltip' align='center'>" . trim($row['cod_fornitore']) . "</td>" .
				"	<td width='208' align='left'>" . trim($row['des_fornitore']) . "</td>" .
				"	<td width='208' align='left'>" . trim($row['des_indirizzo_fornitore']) . "</td>" .
				"	<td width='158' align='left'>" . trim($row['des_citta_fornitore']) . "</td>" .				
				"	<td width='58' align='left'>" . trim($row['cap_fornitore']) . "</td>" .
				"	<td width='58' align='left'>" . trim($row['tip_addebito']) . "</td>" .
				"	<td width='55'  align='right'>" . trim($row['tot_registrazioni_fornitore']) . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneModifica . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$_SESSION['numFornitoriTrovati'] = $numFornitori;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {
		
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%codfornitore%' => $_SESSION["codfornitore"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>