<?php

require_once 'configurazioni.abstract.class.php';

class RicercaContoTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/ricercaConto.form.html";

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

			self::$_instance = new RicercaContoTemplate();

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

		if (isset($_SESSION["contiTrovati"])) {
				
			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='70'>%ml.conto%</th>" .
			"		<th width='400'>%ml.desconto%</th>" .
			"		<th width='150'>%ml.catconto%</th>" .
			"		<th width='100'>%ml.tipconto%</th>" .
			"		<th width='53' colspan='2'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-conti'>" .
			"	<table class='expandible'>" .
			"		<tbody>";
				
			$contiTrovati = $_SESSION["contiTrovati"];
			$numConti = 0;

			foreach(pg_fetch_all($contiTrovati) as $row) {

				if (trim($row['tipo']) == 'C') {

					if ($row['tot_registrazioni_conto'] == 0) {
						$class = "class='parentAperto'";
						$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaContoFacade.class.php?modo=start&codconto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
						$bottoneCancella = "<a class='tooltip' onclick='cancellaConto(" . trim($row['cod_conto']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";						
					}
					else {
						$class = "class='parent'";
						$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaContoFacade.class.php?modo=start&codconto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
						$bottoneCancella = "&nbsp;";
					}

					$numConti ++;
					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . " id='" . trim($row['cod_conto']) . "'>" .
					"	<td width='80' class='tooltip' align='center'>" . trim($row['cod_conto']) . "</td>" .
					"	<td width='408' align='left'>" . trim($row['des_conto']) . "</td>" .
					"	<td width='155' align='center'>" . trim($row['cat_conto']) . "</td>" .
					"	<td width='110' align='center'>" . trim($row['tip_conto']) . "</td>" .
					"	<td height='28' width='25' id='icons'>" . $bottoneModifica . "</td>" .
					"	<td height='28' width='25' id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";
						
				}
				elseif (trim($row['tipo']) == 'S') {

					if ($row['tot_registrazioni_sottoconto'] > 0) {
						$bottoneMastrino = "<a class='tooltip' onclick='generaMastrino(" . trim($row['cod_conto']) . "," . (string)trim($row['cod_sottoconto']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.mastrino%'><span class='ui-icon ui-icon-document'></span></li></a>";
					}
					else {
						$bottoneMastrino = "&nbsp;";
					}
					
					$class = "class='child-" . trim($row['cod_conto']) . "'";
					$id = "id='child'";

					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . " " . $id . " >" .
					"	<td class='tooltip' align='right'>" . trim($row['cod_sottoconto']) . "</td>" .
					"	<td colspan='2' align='left'><i>" . trim($row['des_sottoconto']) . "</i></td>" .
					"	<td colspan='2' align='right'><i>" . trim($row['tot_registrazioni_sottoconto']) . "</i></td>" .
					"	<td height='28' width='25' id='icons'>" . $bottoneMastrino . "</td>" .
					"</tr>";
				}

			}
			$_SESSION['numContiTrovati'] = $numConti;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {
				
		}

		if ($_SESSION["categoria"] == "Conto Economico") $contoEconomicoSelected = "selected";
		if ($_SESSION["categoria"] == "Stato Patrimoniale") $statoPatrimonialeSelected = "selected";

		if ($_SESSION["tipoconto"] == "Dare") $dareSelected = "selected";
		if ($_SESSION["tipoconto"] == "Avere") $avereSelected = "selected";
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => "",
				'%datareg_a%' => "",
				'%contoeconomicoselected%' => $contoEconomicoSelected,
				'%statopatrimonialeselected%' => $statoPatrimonialeSelected,
				'%dareselected%' => $dareSelected,
				'%avereselected%' => $avereSelected,
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>