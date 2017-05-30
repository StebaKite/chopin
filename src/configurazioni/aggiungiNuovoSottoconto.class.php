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
		$sottoconto = Sottoconto::getInstance();
		if ($sottoconto->getQtaSottoconti() > 0) {
			$sottoconto->setNuoviSottoconti($sottoconto->getSottoconti());
		}
		$sottoconto->aggiungiNuovoSottoconto();
		$sottoconto->setSottoconti($sottoconto->getNuoviSottoconti());
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

		$tbody = "";
		$thead =
		"<thead>" .
		"	<tr>" .
		"		<th width='100' align='center'>Sottoconto</th>" .
		"		<th width='400' align='left'>Descrizione</th>" .
		"		<th width='18'>Gruppo</th>" .
		"		<th>&nbsp;</th>" .
		"		<th>&nbsp;</th>" .
		"	</tr>" .
		"</thead>";

		foreach ($sottoconto->getSottoconti() as $row) {

			$bottoneCancella = "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottocontoPagina(" . $tableRow[Sottoconto::COD_SOTTOCONTO] . ",&apos;" . $tableRow[Sottoconto::DES_SOTTOCONTO] . "&apos;)><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";

			$tbody .=
			"<tr>" .
			"	<td>" . $row[Sottoconto::COD_SOTTOCONTO] . "</td>" .
			"	<td>" . $row[Sottoconto::DES_SOTTOCONTO] . "</td>" .
			"	<td>" . $indGruppo . "</td>" .
			self::MODIFICA_GRUPPO_SOTTOCONTO_HREF . '"' . $row[Sottoconto::IND_GRUPPO] . '","' . $row[Sottoconto::COD_SOTTOCONTO] . '","' . $row[Sottoconto::DES_SOTTOCONTO] . '","' . $row[Sottoconto::NUM_REG_SOTTOCONTO] . '"' . self::MODIFICA_GRUPPO_SOTTOCONTO_ICON .
			$bottoneCancella .
			"</tr>";


// 			$tableSottoconti .= "<tr id='" . $tableRow[0] . "'>";
// 			$tableSottoconti .= "<td width='107' align='center'>" . $tableRow[Sottoconto::COD_SOTTOCONTO] . "</td>";
// 			$tableSottoconti .= "<td width='407' align='left'>" . $tableRow[Sottoconto::DES_SOTTOCONTO] . "</td>";
// 			$tableSottoconti .= "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottocontoPagina";
// 			$tableSottoconti .= "(" . $tableRow[Sottoconto::COD_SOTTOCONTO] . ",&apos;" . $tableRow[Sottoconto::DES_SOTTOCONTO] . "&apos;)'";
// 			$tableSottoconti .= "><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
// 			$tableSottoconti .= "</tr>";
		}

		echo "<table id='sottoconti-head' class='result'>" . $thead . $tbody . "</table>";
	}

	public function go()
	{
		$this->start();
	}
}

?>