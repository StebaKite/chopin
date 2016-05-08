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
			"	<thead>" .
			"		<tr>" .
			"			<th>%ml.codfornitore%</th>" .
			"			<th>%ml.desfornitore%</th>" .
			"			<th>%ml.desindirizzofornitore%</th>" .
			"			<th>%ml.descittafornitore%</th>" .
			"			<th>%ml.capfornitore%</th>" .
			"			<th>%ml.tipaddebito%</th>" .
			"			<th>%ml.numggscafatt%</th>" .
			"			<th>%ml.qtareg%</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>" ;
		
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
					$bottoneCancella = "<a class='tooltip' onclick='cancellaFornitore(" . trim($row['id_fornitore']) . "," . trim($row['cod_fornitore']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$bottoneModifica = "<a class='tooltip' href='../anagrafica/modificaFornitoreFacade.class.php?modo=start&idfornitore=" . trim($row['id_fornitore']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}
		
				$numFornitori ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . trim($row['cod_fornitore']) . "</td>" .
				"	<td>" . trim($row['des_fornitore']) . "</td>" .
				"	<td>" . trim($row['des_indirizzo_fornitore']) . "</td>" .
				"	<td>" . trim($row['des_citta_fornitore']) . "</td>" .				
				"	<td>" . trim($row['cap_fornitore']) . "</td>" .
 				"	<td>" . trim($row['tip_addebito']) . "</td>" .
 				"	<td>" . trim($row['num_gg_scadenza_fattura']) . "</td>" .
 				"	<td>" . trim($row['tot_registrazioni_fornitore']) . "</td>" .
 				"	<td id='icons'>" . $bottoneModifica . "</td>" .
 				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$_SESSION['numFornitoriTrovati'] = $numFornitori;
			$risultato_ricerca = $risultato_ricerca . "</tbody>";
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