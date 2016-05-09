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
			"<table id='conti' class='display'>" .
			"	<thead>" .
			"   	<tr>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th>%ml.conto%</th>" .
			"			<th>%ml.sottoconto%</th>" .
			"			<th>%ml.desconto%</th>" .
			"			<th>%ml.catconto%</th>" .
			"			<th>%ml.tipconto%</th>" .
			"			<th></th>" .
			"			<th></th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";
				
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
					"<tr class='dt-bold'>" .
					"	<td>" . trim($row['cod_conto']) . "</td>" .
					"	<td>" . trim($row['cod_sottoconto']) . "</td>" .
					
					"	<td>" . trim($row['cod_conto']) . "</td>" .
					"	<td></td>" .
					"	<td>" . trim($row['des_conto']) . "</td>" .
					"	<td>" . trim($row['cat_conto']) . "</td>" .
					"	<td>" . trim($row['tip_conto']) . "</td>" .
					"	<td id='icons'>" . $bottoneModifica . "</td>" .
					"	<td id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";
						
				}
				elseif (trim($row['tipo']) == 'S') {

					$bottoneMastrino = "<a class='tooltip' onclick='generaMastrino(" . trim($row['cod_conto']) . "," . (string)trim($row['cod_sottoconto']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.mastrino%'><span class='ui-icon ui-icon-document'></span></li></a>";
					
					$class = "class='child-" . trim($row['cod_conto']) . "'";
					$id = "id='child'";

					$risultato_ricerca = $risultato_ricerca .
					"<tr>" .
					"	<td>" . trim($row['cod_conto']) . "</td>" .
					"	<td>" . trim($row['cod_sottoconto']) . "</td>" .
					
					"	<td></td>" .
					"	<td>" . trim($row['cod_sottoconto']) . "</td>" .
					"	<td><i>" . trim($row['des_sottoconto']) . "</i></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td><i>" . trim($row['tot_registrazioni_sottoconto']) . "</i></td>" .
					"	<td id='icons'>" . $bottoneMastrino . "</td>" .
					"</tr>";
				}

			}
			$_SESSION['numContiTrovati'] = $numConti;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => "",
				'%datareg_a%' => "",
				'%contoeconomicoselected%' => ($_SESSION["categoria"] == "Conto Economico") ? "selected" : "",
				'%statopatrimonialeselected%' => ($_SESSION["categoria"] == "Stato Patrimoniale") ? "selected" : "",
				'%dareselected%' => ($_SESSION["tipoconto"] == "Dare") ? "selected" : "",
				'%avereselected%' => ($_SESSION["tipoconto"] == "Avere") ? "selected" : "",
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>