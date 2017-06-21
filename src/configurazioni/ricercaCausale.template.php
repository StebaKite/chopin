<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'causale.class.php';

class RicercaCausaleTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_CAUSALI_TEMPLATE])) $_SESSION[self::RICERCA_CAUSALI_TEMPLATE] = serialize(new RicercaCausaleTemplate());
		return unserialize($_SESSION[self::RICERCA_CAUSALI_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		// Template --------------------------------------------------------------

		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_CAUSALE;
		$risultato_ricerca = "";

		if ($causale->getQtaCausali() > 0) {

			$risultato_ricerca =
			"<table id='causali' class='display'>" .
			"	<thead>" .
			"		<th>%ml.codcausale%</th>" .
			"		<th>%ml.descausale%</th>" .
			"		<th>%ml.catcausale%</th>" .
			"		<th>%ml.qtareg%</th>" .
			"		<th></th>" .
			"		<th></th>" .
			"		<th></th>" .
			"	</thead>" .
			"	<tbody>";

			foreach($causale->getCausali() as $row) {

				if ($row[self::NUM_CONTI_CAUSALE] == 0) {
					$class = "class='errato'";
				}
				else {
					$class = "class=''";
				}

				if ($row[self::NUM_REG_CAUSALE] == 0) {
					$bottoneModifica = self::MODIFICA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::MODIFICA_CAUSALE_ICON;
					$bottoneConfigura = self::CONFIGURA_CAUSALE_HREF  . trim($row[$causale::COD_CAUSALE]) . self::CONFIGURA_CAUSALE_ICON;
					$bottoneCancella = self::CANCELLA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::CANCELLA_CAUSALE_ICON;
				}
				else {
					$bottoneModifica = self::MODIFICA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::MODIFICA_CAUSALE_ICON;
					$bottoneConfigura = self::CONFIGURA_CAUSALE_HREF  . trim($row[$causale::COD_CAUSALE]) . self::CONFIGURA_CAUSALE_ICON;
					$bottoneCancella = "&nbsp;";
				}

				$risultato_ricerca .=
				"<tr>" .
				"	<td>" . trim($row[$causale::COD_CAUSALE]) . "</td>" .
				"	<td>" . trim($row[$causale::DES_CAUSALE]) . "</td>" .
				"	<td>" . trim($row[$causale::CAT_CAUSALE]) . "</td>" .
				"	<td>" . trim($row[self::NUM_REG_CAUSALE]) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"	<td id='icons'>" . $bottoneConfigura . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$risultato_ricerca .= "</tbody></table>";
		}

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO],
				'%azione%' => $_SESSION[self::AZIONE],
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%causale%' => $causale->getCodCausale(),
				'%risultato_ricerca%' => $risultato_ricerca,
				'%codcausale%' => $causale->getCodCausale(),
				'%descausale%' => $causale->getDesCausale(),
				'%catcausale%' => $causale->getCatCausale()
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>