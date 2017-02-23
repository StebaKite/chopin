<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once 'utility.class.php';

class RicercaClienteTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance() {

		if (!isset($_SESSION[self::RICERCA_CLIENTE_TEMPLATE])) $_SESSION[self::RICERCA_CLIENTE_TEMPLATE] = serialize(new RicercaClienteTemplate());
		return unserialize($_SESSION[self::RICERCA_CLIENTE_TEMPLATE]);
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_CLIENTE;
		$risultato_ricerca = "";

		if (isset($_SESSION[self::CLIENTI])) {

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

			$clientiTrovati = $_SESSION[self::CLIENTI];
			$numClienti = 0;

			foreach(pg_fetch_all($clientiTrovati) as $row) {

				if ($row['tot_registrazioni_cliente'] == 0) {
					$bottoneModifica = self::MODIFICA_CLIENTE_HREF . trim($row['id_cliente']) . self::MODIFICA_CLIENTE_ICON;
					$bottoneCancella = self::CANCELLA_CLIENTE_HREF . trim($row['id_cliente']) . "," . trim($row['cod_cliente']) . self::CANCELLA_CLIENTE_ICON;
				}
				else {
					$bottoneModifica = self::MODIFICA_CLIENTE_HREF . trim($row['id_cliente']) . self::MODIFICA_CLIENTE_ICON;
					$bottoneCancella = "&nbsp;";
				}

				$numClienti ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . trim($row[self::CODICE_CLIENTE]) . "</td>" .
				"	<td>" . trim($row[self::DESCRIZIONE_CLIENTE]) . "</td>" .
				"	<td>" . trim($row[self::INDIRIZZO_CLIENTE]) . "</td>" .
				"	<td>" . trim($row[self::CITTA_CLIENTE]) . "</td>" .
				"	<td>" . trim($row[self::CAP_CLIENTE]) . "</td>" .
				"	<td>" . trim($row[self::TIP_ADDEBITO]) . "</td>" .
				"	<td>" . trim($row[self::QTA_REGISTRAZIONI_CLIENTE]) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$_SESSION[self::QTA_CLIENTI] = $numClienti;
			$risultato_ricerca = $risultato_ricerca . "</tbody>";
		}
		else {

		}

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO],
				'%azione%' => $_SESSION[self::AZIONE_RICERCA_CLIENTE],
				'%codcliente%' => $_SESSION[self::CODICE_CLIENTE],
				'%elenco_categorie_cliente%' => $_SESSION[SELF::CATEGORIE_CLIENTE],
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>