<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
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
		if (!isset($_SESSION[self::AGGIUNGI_SOTTOCONTO])) $_SESSION[self::AGGIUNGI_SOTTOCONTO] = serialize(new AggiungiNuovoSottoconto());
		return unserialize($_SESSION[self::AGGIUNGI_SOTTOCONTO]);
	}

	public function start()
	{
		$db = Database::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$sottoconto->aggiungiNuovoSottoconto($db);
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

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
			$tableSottoconti .= "<td width='107' align='center'>" . $tableRow[0] . "</td>";
			$tableSottoconti .= "<td width='407' align='left'>" . $tableRow[1] . "</td>";
			$tableSottoconti .= "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottocontoPagina";
			$tableSottoconti .= "(" . $tableRow[0] . ",&apos;" . $tableRow[1] . "&apos;)'";
			$tableSottoconti .= "><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
			$tableSottoconti .= "</tr>";
		}

		$tableSottoconti .= "</tbody></table>";
		echo $tableSottoconti;
	}

	public function go()
	{
		$this->start();
	}
}

?>