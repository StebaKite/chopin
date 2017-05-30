<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'sottoconto.class.php';

class TogliNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
{
	function __construct() {

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
		if (!isset($_SESSION[self::TOGLI_SOTTOCONTO])) $_SESSION[self::TOGLI_SOTTOCONTO] = serialize(new TogliNuovoSottoconto());
		return unserialize($_SESSION[self::TOGLI_SOTTOCONTO]);
	}

	public function start()
	{
		$sottoconto = Sottoconto::getInstance();
		$sottoconto->togliNuovoSottoconto();
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

		$tableSottoconti = "";

		if (sizeof($sottoconto->getNuoviSottoconti()) > 0) {
			$tableSottoconti =
			"<table id='sottoconti-head' class='result'>" .
			"<thead>" .
			"<tr>" .
			"<th width='100' align='center'>Sottoconto</th>" .
			"<th width='400' align='left'>Descrizione</th>" .
			"<th width='23'>&nbsp;</th>" .
			"</tr>" .
			"</thead>" .
			"<tbody>";

			foreach ($sottoconto->getNuoviSottoconti() as $tableRow) {

				$tableSottoconti .= "<tr id='" . $tableRow[0] . "'>";
				$tableSottoconti .= "<td width='107' align='center'>" . $tableRow[Sottoconto::COD_SOTTOCONTO] . "</td>";
				$tableSottoconti .= "<td width='407' align='left'>" . $tableRow[Sottoconto::DES_SOTTOCONTO] . "</td>";
				$tableSottoconti .= "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottocontoPagina(";
				$tableSottoconti .= $tableRow[Sottoconto::COD_SOTTOCONTO];
				$tableSottoconti .= ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
				$tableSottoconti .= "</tr>";
			}
			$tableSottoconti .= "</tbody></table>";
		}
		echo $tableSottoconti;
	}

	public function go()
	{
		$this->start();
	}
}

?>