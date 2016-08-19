<?php

require_once 'anagrafica.abstract.class.php';

class RicercaMercatoTemplate extends AnagraficaAbstract {

	private static $_instance = null;

	private static $pagina = "/anagrafica/ricercaMercato.form.html";

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

			self::$_instance = new RicercaMercatoTemplate();

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
	
		if (isset($_SESSION["mercatiTrovati"])) {
	
			$risultato_ricerca =
			"	<thead>" .
			"		<tr> " .
			"			<th class='dt-left'>%ml.codmercato%</th>" .
			"			<th class='dt-left'>%ml.desmercato%</th>" .
			"			<th class='dt-left'>%ml.cittamercato%</th>" .
			"			<th class='dt-left'>%ml.qtareg%</th>" .
			"			<th class='dt-left'></th>" .
			"			<th class='dt-left'></th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";
	
			$mercatiTrovati = $_SESSION["mercatiTrovati"];
			$numMercati = 0;
	
			foreach(pg_fetch_all($mercatiTrovati) as $row) {
	
				if ($row['tot_registrazioni_mercato'] == 0) {
					$parms = trim($row['id_mercato']) . "#" . trim($row['cod_mercato']) . "#" . str_replace("'", "@", trim($row['des_mercato'])) . "#" . str_replace("'", "@", trim($row['citta_mercato']));
					$bottoneModifica = "<a class='tooltip' onclick='modificaMercato(" . '"' . $parms . '"' . ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "<a class='tooltip' onclick='cancellaMercato(" . trim($row['id_mercato']) . "," . trim($row['cod_mercato']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$parms = trim($row['id_mercato']) . "#" . trim($row['cod_mercato']) . "#" . str_replace("'", "@", trim($row['des_mercato'])) . "#" . str_replace("'", "@", trim($row['citta_mercato']));
					$bottoneModifica = "<a class='tooltip' onclick='modificaMercato(" . '"' . $parms . '"' . ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}
	
				$numMercati ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . trim($row['cod_mercato']) . "</td>" .
				"	<td>" . trim($row['des_mercato']) . "</td>" .
				"	<td>" . trim($row['citta_mercato']) . "</td>" .
				"	<td>" . trim($row['tot_registrazioni_mercato']) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$_SESSION['numMercatiTrovati'] = $numMercati;
			$risultato_ricerca = $risultato_ricerca . "</tbody>";
		}
		else {
	
		}
	
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>