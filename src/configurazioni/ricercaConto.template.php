	<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class RicercaContoTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_CONTO_TEMPLATE])) $_SESSION[self::RICERCA_CONTO_TEMPLATE] = serialize(new RicercaContoTemplate());
		return unserialize($_SESSION[self::RICERCA_CONTO_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		// Template --------------------------------------------------------------

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_CONTO;
		$risultato_ricerca = "";

		if ($conto->getQtaConti() > 0) {

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

			foreach($conto->getConti()  as $row) {

				if (trim($row['tipo']) == 'C') {

					if ($row[self::NUM_REG_CONTO] == 0) {
						$class = "class='parentAperto'";
						$bottoneModifica = self::MODIFICA_CONTO_HREF . trim($row[$conto::COD_CONTO]) . self::MODIFICA_CONTO_ICON;
						$bottoneCancella = self::CANCELLA_CONTO_HREF . trim($row[$conto::COD_CONTO]) . self::CANCELLA_CONTO_ICON;
					}
					else {
						$class = "class='parent'";
						$bottoneModifica = self::MODIFICA_CONTO_HREF . trim($row[$conto::COD_CONTO]) . self::MODIFICA_CONTO_ICON;
						$bottoneCancella = "&nbsp;";
					}

					$risultato_ricerca .=
					"<tr class='dt-bold'>" .
					"	<td>" . trim($row[$conto::COD_CONTO]) . "</td>" .
					"	<td>" . trim($row[$sottoconto::COD_SOTTOCONTO]) . "</td>" .

					"	<td>" . trim($row[$conto::COD_CONTO]) . "</td>" .
					"	<td></td>" .
					"	<td>" . trim($row[$conto::DES_CONTO]) . "</td>" .
					"	<td>" . trim($row[$conto::CAT_CONTO]) . "</td>" .
					"	<td>" . trim($row[$conto::TIP_CONTO]) . "</td>" .
					"	<td id='icons'>" . $bottoneModifica . "</td>" .
					"	<td id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";

				}
				elseif (trim($row['tipo']) == 'S') {

					$bottoneMastrino = self::GENERA_MASTRINO_HREF . trim($row[$conto::COD_CONTO]) . "," . (string)trim($row[$sottoconto::COD_SOTTOCONTO]) . self::GENERA_MASTRINO_ICON;

					$class = "class='child-" . trim($row['cod_conto']) . "'";
					$id = "id='child'";

					$risultato_ricerca .=
					"<tr>" .
					"	<td>" . trim($row[$conto::COD_CONTO]) . "</td>" .
					"	<td>" . trim($row[$sottoconto::COD_SOTTOCONTO]) . "</td>" .

					"	<td></td>" .
					"	<td>" . trim($row[$sottoconto::COD_SOTTOCONTO]) . "</td>" .
					"	<td><i>" . trim($row[$sottoconto::DES_SOTTOCONTO]) . "</i></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td><i>" . trim($row[self::NUM_REG_SOTTOCONTO]) . "</i></td>" .
					"	<td id='icons'>" . $bottoneMastrino . "</td>" .
					"</tr>";
				}

			}
			$risultato_ricerca .= "</tbody></table>";
		}

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO],
				'%azione%' => $_SESSION[self::AZIONE],
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%datareg_da%' => "",
				'%datareg_a%' => "",
				'%contoeconomicoselected%' => ($conto->getCatConto() == "Conto Economico") ? "selected" : "",
				'%statopatrimonialeselected%' => ($conto->getCatConto() == "Stato Patrimoniale") ? "selected" : "",
				'%dareselected%' => ($conto->getTipConto() == "Dare") ? "selected" : "",
				'%avereselected%' => ($conto->getTipConto() == "Avere") ? "selected" : "",
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
