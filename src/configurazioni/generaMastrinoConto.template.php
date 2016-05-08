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
			"<table id='mastrino' class='display' width='100%'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th class='dt-left'>%ml.datReg%</th>" .
			"			<th class='dt-left'>%ml.descreg%</th>" .
			"			<th class='dt-right'>%ml.dare%</th>" .
			"			<th class='dt-right'>%ml.avere%</th>" .
			"			<th class='dt-right'>%ml.saldoprogressivo%</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";

			$registrazioniTrovate = $_SESSION["registrazioniTrovate"];
			$totaleDare = 0;
			$totaleAvere = 0;
			$saldo = 0;
			
			foreach(pg_fetch_all($registrazioniTrovate) as $row) {

				$class = "class=''";

				if ($row['ind_dareavere'] == 'D') {
					$totaleDare = $totaleDare + $row['imp_registrazione'];
					$impDare = number_format(round($row['imp_registrazione'],2), 2, ',', '.');
					$impAvere = "";
				}
				elseif ($row['ind_dareavere'] == 'A') {
					$totaleAvere = $totaleAvere + $row['imp_registrazione'];
					$impDare = "";
					$impAvere = number_format(round($row['imp_registrazione'],2), 2, ',', '.');
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
					$class = "dt-ko";
				}
				
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . date("d/m/Y",strtotime($row['dat_registrazione'])) . "</td>" .
				"	<td>" . trim($row['des_registrazione']) . "</td>" .
				"	<td class='dt-right'>" . $impDare . "</td>" .
				"	<td class='dt-right'>" . $impAvere . "</td>" .
				"	<td class='dt-right " . $class . "'>" . number_format(round($saldo,2), 2, ',', '.') . "</td>" .
				"</tr>";
			}
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";
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
				'%tipoconto%' => $_SESSION["tipoconto"],
				'%categoria%' => $_SESSION["categoria"],
				'%dessottoconto%' => $des_sottoconto,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf'],
				'%risultato_ricerca%' => $risultato_ricerca,
				'%saldiInclusi%' => $_SESSION["saldiInclusi"],
				'%saldiInclusichecked%' => ($_SESSION["saldiInclusi"] == "S") ? "checked" : "",
				'%saldiEsclusichecked%' => ($_SESSION["saldiInclusi"] == "N") ? "checked" : ""
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
