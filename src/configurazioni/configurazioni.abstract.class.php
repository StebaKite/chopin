<?php

require_once 'nexus6.abstract.class.php';

abstract class ConfigurazioniAbstract extends Nexus6Abstract {

	public static $messaggio;

	const NESSUNO = "NS";
	const COSTI_FISSI = "CF";
	const COSTI_VARIABILI = "CV";
	const RICAVI = "RC";

	// Bottoni

	const CANCELLA_SOTTOCONTO_HREF = "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottoconto(";
	const CANCELLA_SOTTOCONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
	const MODIFICA_GRUPPO_SOTTOCONTO_HREF = "<td id='icons'><a class='tooltip' onclick='modificaGruppoSottoconto(";
	const MODIFICA_GRUPPO_SOTTOCONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='Cambia gruppo'><span class='ui-icon ui-icon-tag'></span></li></a></td>";

	const ESCLUDI_CONTO_HREF = "<a class='tooltip' onclick='escludiConto(";
	const ESCLUDI_CONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.escludiContoTip%'><span class='ui-icon ui-icon-minus'></span></li></a>";
	const INCLUDI_CONTO_HREF = "<a class='tooltip' onclick='includiConto(";
	const INCLUDI_CONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.includiContoTip%'><span class='ui-icon ui-icon-plus'></span></li></a>";

	// Metodi comuni di utilita della prima note ---------------------------

	public function makeTabellaSottoconti($conto, $sottoconto)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$thead = "";
		$tbody = "";

		if ($sottoconto->getQtaSottoconti() > 0) {
			$tbody = "<tbody>";
			$thead =
			"<thead>" .
			"	<tr>" .
			"		<th width='100' align='center'>Sottoconto</th>" .
			"		<th width='400' align='left'>Descrizione</th>" .
			"		<th width='50'>Gruppo</th>" .
			"		<th>&nbsp;</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
			"</thead>";

			foreach ($sottoconto->getSottoconti() as $row)
			{
				$bottoneCancella = "<td width='28' align='right'>" . $row[Sottoconto::NUM_REG_SOTTOCONTO] . "</td>";

				if ($row[Sottoconto::NUM_REG_SOTTOCONTO] == 0) {
					$bottoneCancella = self::CANCELLA_SOTTOCONTO_HREF . $row[Sottoconto::COD_SOTTOCONTO] . "," . $sottoconto->getCodConto() . ",&apos;_mod&apos;" . self::CANCELLA_SOTTOCONTO_ICON ;
				}

				if ($row[Sottoconto::IND_GRUPPO] == "") $indGruppo = "&ndash;&ndash;&ndash;";
				elseif ($row[Sottoconto::IND_GRUPPO] == self::NESSUNO) $indGruppo = "&ndash;&ndash;&ndash;";
				elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_FISSI) $indGruppo = "Costi Fissi";
				elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_VARIABILI) $indGruppo = "Costi Variabili";
				elseif ($row[Sottoconto::IND_GRUPPO] == self::RICAVI) $indGruppo = "Ricavi";

				$tbody .=
				"<tr>" .
				"	<td align='center'>" . $row[Sottoconto::COD_SOTTOCONTO] . "</td>" .
				"	<td>" . $row[Sottoconto::DES_SOTTOCONTO] . "</td>" .
				"	<td align='center'>" . $indGruppo . "</td>" .
				self::MODIFICA_GRUPPO_SOTTOCONTO_HREF . "&apos;" . $row[Sottoconto::IND_GRUPPO] . "&apos;," . $row[Sottoconto::COD_CONTO] . "," . $row[Sottoconto::COD_SOTTOCONTO] . self::MODIFICA_GRUPPO_SOTTOCONTO_ICON .
				$bottoneCancella .
				"</tr>";
			}
			$tbody .= "</tbody>";
		}
		return "<table id='sottocontiTable_mod' class='result'>" . $thead . $tbody . "</table>";
	}

	public function makeTableContiConfigurati($configurazioneCausale)
	{
		if ($configurazioneCausale->getQtaContiConfigurati() > 0) {
			$elencoContiConfigurati = "<table class='result' id='elencoContiConfigurati'><tbody>";
			foreach ($configurazioneCausale->getContiConfigurati() as $row) {

				$bottoneEscludi = self::ESCLUDI_CONTO_HREF . $row[ConfigurazioneCausale::COD_CONTO] . self::ESCLUDI_CONTO_ICON;

				$elencoContiConfigurati .= "<tr " . $class . ">";
				$elencoContiConfigurati .= "<td align='center' width='68'>" . $row[ConfigurazioneCausale::COD_CONTO] . "</td>";
				$elencoContiConfigurati .= "<td align='left' width='400'>" . $row[Conto::DES_CONTO] . "</td>";
				$elencoContiConfigurati .= "<td width='20' id='icons'>" . $bottoneEscludi . "</td>";
				$elencoContiConfigurati .= "</tr>";
			}
			$elencoContiConfigurati .= "</tbody></table></div>";
		}
		else {
			$elencoContiConfigurati = "<p>La causale non ha conti configurati</p>";
		}
		return $elencoContiConfigurati;
	}

	public function makeTableContiConfigurabili($configurazioneCausale)
	{
		if ($configurazioneCausale->getQtaContiConfigurabili() > 0) {
			$elencoContiConfigurabili = "<table class='result' id='elencoContiDisponibili'><tbody>";
			foreach ($configurazioneCausale->getContiConfigurabili() as $row) {

				$bottoneIncludi = self::INCLUDI_CONTO_HREF . $row[ConfigurazioneCausale::COD_CONTO] . self::INCLUDI_CONTO_ICON;

				$elencoContiConfigurabili .= "<tr " . $class . ">";
				$elencoContiConfigurabili .= "<td width='25' id='icons'>" . $bottoneIncludi . "</td>";
				$elencoContiConfigurabili .= "<td align='center' width='68'>" . $row[ConfigurazioneCausale::COD_CONTO] . "</td>";
				$elencoContiConfigurabili .= "<td align='left' width='400'>" . $row[Conto::DES_CONTO] . "</td>";
				$elencoContiConfigurabili .= "</tr>";
			}
			$elencoContiConfigurabili .= "</tbody></table></div>";
		}
		else {
			$elencoContiConfigurabili = "<p>Non ci sono conti disponibili da includere nella causale</p>";
		}
		return $elencoContiConfigurabili;
	}

	public function refreshContiConfigurati($db, $configurazioneCausale)
	{
		$CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";

		if (!$configurazioneCausale->loadContiConfigurati($db)) {
			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
			return false;
		}
		$_SESSION[$CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
		return true;
	}

	public function refreshContiConfigurabili($db, $configurazioneCausale)
	{
		$CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";

		if (!$configurazioneCausale->loadContiConfigurabili($db)) {
			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
			return false;
		}
		$_SESSION[$CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
		return true;
	}

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	public function getMessaggio() {
		return self::$messaggio;
	}
}

?>
