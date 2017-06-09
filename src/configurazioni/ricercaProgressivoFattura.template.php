<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';
require_once 'categoriaCliente.class.php';

class RicercaProgressivoFatturaTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_PROGRESSIVO_FATTURA_TEMPLATE])) $_SESSION[self::RICERCA_PROGRESSIVO_FATTURA_TEMPLATE] = serialize(new RicercaProgressivoFatturaTemplate());
		return unserialize($_SESSION[self::RICERCA_PROGRESSIVO_FATTURA_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		$progressivoFattura = ProgressivoFattura::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_PROGRESSIVO_FATTURA;
		$risultato_ricerca = "";

		if ($progressivoFattura->getQtaProgressiviFattura() > 0) {

			$risultato_ricerca =
			"<table id='progressivi' class='display'>" .
			"	<thead>" .
			"		<th>%ml.categoria%</th>" .
			"		<th>%ml.negozio%</th>" .
			"		<th>%ml.numfatt%</th>" .
			"		<th></th>" .
			"	</thead>" .
			"	<tbody>";

			foreach($progressivoFattura->getProgressiviFattura() as $row) {

				$bottoneModifica = self::MODIFICA_PROGRESSIVO_HREF . trim($row[ProgressivoFattura::CAT_CLIENTE]) . ",&apos;" . trim($row[ProgressivoFattura::NEG_PROGR]) . "&apos;" . self::MODIFICA_PROGRESSIVO_ICON;

				$risultato_ricerca .=
				"<tr>" .
				"	<td>" . trim($row[CategoriaCliente::DES_CATEGORIA]) . "</td>" .
				"	<td>" . trim($row[ProgressivoFattura::NEG_PROGR]) . "</td>" .
				"	<td>" . trim($row[ProgressivoFattura::NUM_FATTURA_ULTIMO]) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"</tr>";
			}
			$risultato_ricerca .= "</tbody></table></div>";
		}

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO],
				'%azione%' => $_SESSION[self::AZIONE],
				'%elenco_categorie_cliente%' => $_SESSION['elenco_categorie_cliente'],
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%risultato_ricerca%' => $risultato_ricerca
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>