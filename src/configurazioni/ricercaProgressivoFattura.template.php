<?php

require_once 'configurazioni.abstract.class.php';

class RicercaProgressivoFatturaTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/ricercaProgressivoFattura.form.html";

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

			self::$_instance = new RicercaProgressivoFatturaTemplate();

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

		if (isset($_SESSION["progressiviTrovati"])) {

			$risultato_ricerca =
			"<table id='progressivi' class='display'>" .
			"	<thead>" .
			"		<th>%ml.categoria%</th>" .
			"		<th>%ml.negozio%</th>" .
			"		<th>%ml.numfatt%</th>" .
			"		<th></th>" .
			"	</thead>" .
			"	<tbody>";

			$progressiviTrovati = $_SESSION["progressiviTrovati"];
			$numProgressivi = 0;

			foreach(pg_fetch_all($progressiviTrovati) as $row) {


				$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaProgressivoFatturaFacade.class.php?modo=start&catcliente=" . trim($row['cat_cliente']) . "&codneg=" . trim($row['neg_progr']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";

				$numProgressivi ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . trim($row['cat_cliente']) . "</td>" .
				"	<td>" . trim($row['neg_progr']) . "</td>" .
				"	<td>" . trim($row['num_fattura_ultimo']) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"</tr>";
			}
			$_SESSION['numProgressiviTrovati'] = $numProgressivi;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {

		}

		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%catcliente%' => $_SESSION["codcliente"],
				'%elenco_categorie_cliente%' => $_SESSION['elenco_categorie_cliente'],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>