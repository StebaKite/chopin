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
			"		<th width='83' colspan='3'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-conti'>" .
			"	<table class='expandible'>" .
			"		<tbody>";
				
			$contiTrovati = $_SESSION["contiTrovati"];
			$numConti = 0;

			foreach(pg_fetch_all($contiTrovati) as $row) {

				if (trim($row['tipo']) == 'C') {

					switch ($row['sta_conto']) {
						case ("00"): {
							$class = "class='parentAperto'";
							$bottoneModifica = "&nbsp;";
							$bottoneCancella = "&nbsp;";
// 							$bottoneModifica = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
// 							$bottoneCancella = "<a class='tooltip' onclick='cancellaRegistrazione(" . trim($row['id_registrazione']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
							break;
						}
						default: {
							$class = "class='parent'";
							$bottoneModifica = "&nbsp;";
							$bottoneCancella = "&nbsp;";
							break;
						}
					}
						
					$numConti ++;
					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . " id='" . trim($row['cod_conto']) . "'>" .
					"	<td width='80' class='tooltip' align='center'>" . trim($row['cod_conto']) . "</td>" .
					"	<td width='410' align='left'>" . trim($row['des_conto']) . "</td>" .
					"	<td width='155' align='center'>" . trim($row['cat_conto']) . "</td>" .
					"	<td width='110' align='center'>" . trim($row['tip_conto']) . "</td>" .
					"	<td width='30' id='icons'><a class='tooltip' href='../configurazioni/visualizzaContoFacade.class.php?modo=start&codConto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizza%'><span class='ui-icon ui-icon-search'></span></li></a></td>" .
					"	<td width='30' id='icons'>" . $bottoneModifica . "</td>" .
					"	<td width='30' id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";
						
				}
				elseif (trim($row['tipo']) == 'S') {

					$class = "class='child-" . trim($row['cod_conto']) . "'";
					$id = "id='child'";

					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . " " . $id . " >" .
					"	<td class='tooltip' align='right'>" . trim($row['cod_sottoconto']) . "</td>" .
					"	<td colspan='6' align='left'><i>" . trim($row['des_sottoconto']) . "</i></td>" .
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
		
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%contoeconomicoselected%' => $contoEconomicoSelected,
				'%statopatrimonialeselected%' => $statoPatrimonialeSelected,
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>