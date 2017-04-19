<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'configurazioneCausale.class.php';
require_once 'conto.class.php';
require_once 'utility.class.php';

class ConfiguraCausaleTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();

		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CONFIGURA_CAUSALE_TEMPLATE])) $_SESSION[self::CONFIGURA_CAUSALE_TEMPLATE] = serialize(new ConfiguraCausaleTemplate());
		return unserialize($_SESSION[self::CONFIGURA_CAUSALE_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		$configurazioneCausale = ConfigurazioneCausale::getInstance();
		$conto = Conto::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$class = "class=''";

		$form = $this->root . $array['template'] . self::PAGINA_CONFIGURA_CAUSALE;

		// Prepara la tabella dei conti configurati sulla causale

		if (sizeof($configurazioneCausale->getContiConfigurati()) > 0) {

			$elencoContiConfigurati =
			"<div class='scroll-config-causali'>" .
			"	<table class='result'>" .
			"		<tbody>";

			foreach ($configurazioneCausale->getContiConfigurati() as $row) {

				$bottoneEscludi = self::ESCLUDI_CONTO_HREF . $row[ConfigurazioneCausale::COD_CONTO] . self::ESCLUDI_CONTO_ICON;

				$elencoContiConfigurati .= "<tr " . $class . ">";
				$elencoContiConfigurati .= "<td align='center' width='68'>" . $row[ConfigurazioneCausale::COD_CONTO] . "</td>";
				$elencoContiConfigurati .= "<td align='left' width='600'>" . $row[Conto::DES_CONTO] . "</td>";
				$elencoContiConfigurati .= "<td width='20' id='icons'>" . $bottoneEscludi . "</td>";
				$elencoContiConfigurati .= "</tr>";
			}
			$elencoContiConfigurati .= "</tbody></table></div>";
		}
		else {
			$elencoContiConfigurati = "<p>La causale non ha conti configurati</p>";
		}

		// Prepara la tabella dei conti disponibili non ancora configurati sulla causale

		if (sizeof($configurazioneCausale->getContiConfigurabili()) > 0) {

			$elencoContiConfigurabili =
			"<div class='scroll-config-causali'>" .
			"	<table class='result'>" .
			"		<tbody>";

			foreach ($configurazioneCausale->getContiConfigurabili() as $row) {

				$bottoneIncludi = self::INCLUDI_CONTO_HREF . $row[ConfigurazioneCausale::COD_CONTO] . self::INCLUDI_CONTO_ICON;

				$elencoContiConfigurabili .= "<tr " . $class . ">";
				$elencoContiConfigurabili .= "<td width='25' id='icons'>" . $bottoneIncludi . "</td>";
				$elencoContiConfigurabili .= "<td align='center' width='68'>" . $row[ConfigurazioneCausale::COD_CONTO] . "</td>";
				$elencoContiConfigurabili .= "<td align='left' width='600'>" . $row[Conto::DES_CONTO] . "</td>";
				$elencoContiConfigurabili .= "</tr>";
			}
			$elencoContiConfigurabili .= "</tbody></table></div>";
		}
		else {
			$elencoContiConfigurabili = "<p>Non ci sono conti disponibili da includere nella causale</p>";
		}

		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%codcausale%' => $configurazioneCausale->getCodCausale(),
				'%descausale%' => $configurazioneCausale->getDesCausale(),
				'%elencoconticausale%' => $elencoContiConfigurati,
				'%elencocontidisponibili%' => $elencoContiConfigurabili
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>