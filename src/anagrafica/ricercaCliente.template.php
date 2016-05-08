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
			"	<thead>" .
			"		<tr> " .
			"			<th>%ml.codcliente%</th>" .
			"			<th>%ml.descliente%</th>" .
			"			<th>%ml.desindirizzocliente%</th>" .
			"			<th>%ml.descittacliente%</th>" .
			"			<th>%ml.capcliente%</th>" .
			"			<th>%ml.tipaddebito%</th>" .
			"			<th>%ml.qtareg%</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";

			$clientiTrovati = $_SESSION["clientiTrovati"];
			$numClienti = 0;

			foreach(pg_fetch_all($clientiTrovati) as $row) {

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
				"<tr>" .
				"	<td>" . trim($row['cod_cliente']) . "</td>" .
				"	<td>" . trim($row['des_cliente']) . "</td>" .
				"	<td>" . trim($row['des_indirizzo_cliente']) . "</td>" .
				"	<td>" . trim($row['des_citta_cliente']) . "</td>" .
				"	<td>" . trim($row['cap_cliente']) . "</td>" .
				"	<td>" . trim($row['tip_addebito']) . "</td>" .
				"	<td>" . trim($row['tot_registrazioni_cliente']) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$_SESSION['numClientiTrovati'] = $numClienti;
			$risultato_ricerca = $risultato_ricerca . "</tbody>";
		}
		else {

		}

		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%codcliente%' => $_SESSION["codcliente"],
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