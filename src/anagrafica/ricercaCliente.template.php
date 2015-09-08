<?php

require_once 'anagrafica.abstract.class.php';

class RicercaClienteTemplate extends AnagraficaAbstract {

	private static $_instance = null;

	private static $pagina = "/anagrafica/ricercaCliente.form.html";

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

			self::$_instance = new RicercaClienteTemplate();

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

		if (isset($_SESSION["clientiTrovati"])) {

			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='60'>%ml.codcliente%</th>" .
			"		<th width='250'>%ml.descliente%</th>" .
			"		<th width='200'>%ml.desindirizzocliente%</th>" .
			"		<th width='150'>%ml.descittacliente%</th>" .
			"		<th width='50'>%ml.capcliente%</th>" .
			"		<th width='60'>%ml.tipaddebito%</th>" .
			"		<th width='47'>%ml.qtareg%</th>" .
			"		<th width='52' colspan='2'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-clienti'>" .
			"	<table class='result'>" .
			"		<tbody>";

			$clientiTrovati = $_SESSION["clientiTrovati"];
			$numClienti = 0;

			foreach(pg_fetch_all($clientiTrovati) as $row) {

				if ($row['tot_registrazioni_cliente'] == 0) {
					$class = "class=''";
				}
				else {
					$class = "class=''";
				}

				if ($row['tot_registrazioni_cliente'] == 0) {
					$bottoneModifica = "<a class='tooltip' href='../anagrafica/modificaClienteFacade.class.php?modo=start&idcliente=" . trim($row['id_cliente']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "<a class='tooltip' onclick='cancellaCliente(" . trim($row['id_cliente']) . "," . trim($row['cod_cliente']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$bottoneModifica = "<a class='tooltip' href='../anagrafica/modificaClienteFacade.class.php?modo=start&idcliente=" . trim($row['id_cliente']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}

				$numClienti ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr " . $class . " id='" . trim($row['id_cliente']) . "'>" .
				"	<td width='68' class='tooltip' align='center'>" . trim($row['cod_cliente']) . "</td>" .
				"	<td width='258' align='left'>" . trim($row['des_cliente']) . "</td>" .
				"	<td width='208' align='left'>" . trim($row['des_indirizzo_cliente']) . "</td>" .
				"	<td width='158' align='left'>" . trim($row['des_citta_cliente']) . "</td>" .
				"	<td width='58' align='center'>" . trim($row['cap_cliente']) . "</td>" .
				"	<td width='68' align='center'>" . trim($row['tip_addebito']) . "</td>" .
				"	<td width='55'  align='right'>" . trim($row['tot_registrazioni_cliente']) . "</td>" .
				"	<td width='25' id='icons'>" . $bottoneModifica . "</td>" .
				"	<td width='25' id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$_SESSION['numClientiTrovati'] = $numClienti;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {

		}

		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%codcliente%' => $_SESSION["codcliente"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>