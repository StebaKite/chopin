<?php

require_once 'configurazioni.abstract.class.php';

class GeneraMastrinoContoTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/generaMastrinoConto.form.html";

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

			self::$_instance = new GeneraMastrinoContoTemplate();

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

		if (isset($_SESSION["registrazioniTrovate"])) {

			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='100'>%ml.datReg%</th>" .
			"		<th width='350'>%ml.descreg%</th>" .
			"		<th width='100'>%ml.dare%</th>" .
			"		<th width='100'>%ml.avere%</th>" .
			"		<th width='100'>%ml.saldoprogressivo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-mastrino'>" .
			"	<table class='result'>" .
			"		<tbody>";

			$registrazioniTrovate = $_SESSION["registrazioniTrovate"];
			$totaleDare = 0;
			$totaleAvere = 0;
			$saldo = 0;
			
			foreach(pg_fetch_all($registrazioniTrovate) as $row) {

				$class = "class=''";

				if ($row['ind_dareavere'] == 'D') {
					$totaleDare = $totaleDare + $row['imp_registrazione'];
					$impDare = "&euro;" . $row['imp_registrazione'];
					$impAvere = "";
				}
				elseif ($row['ind_dareavere'] == 'A') {
					$totaleAvere = $totaleAvere + $row['imp_registrazione'];
					$impDare = "";
					$impAvere = "&euro;" . $row['imp_registrazione'];
				}

				if (trim($row['tip_conto']) == "Dare") {
					$saldo = $totaleDare - $totaleAvere;						
				}
				elseif (trim($row['tip_conto']) == "Avere") {
					$saldo = $totaleAvere - $totaleDare;
				}
				
				/**
				 * Evidenzia la riga se il saldo è negativo
				 */
				if ($saldo < 0) {
					$class = "class='ko'";
				}
				
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td width='108' class='tooltip' align='center'>" . trim($row['dat_registrazione']) . "</td>" .
				"	<td width='358' align='left'>" . trim($row['des_registrazione']) . "</td>" .
				"	<td width='108' align='right'>" . $impDare . "</td>" .
				"	<td width='108' align='right'>" . $impAvere . "</td>" .
				"	<td width='108' align='right' " . $class . ">&euro;" . $saldo . "</td>" .
				"</tr>";
			}
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
			$des_conto = trim($row["des_conto"]);
			$cat_conto = trim($row["cat_conto"]);
			$des_sottoconto = trim($row["des_sottoconto"]);
		}
		else {

		}

		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%codconto%' => $_SESSION["codconto"],
				'%codsottoconto%' => $_SESSION["codsottoconto"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%desconto%' => $des_conto,
				'%catconto%' => $cat_conto,  
				'%dessottoconto%' => $des_sottoconto,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf'],
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>