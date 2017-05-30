<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.presentation.interface.php';
require_once 'utility.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'fornitore.class.php';
require_once 'registrazione.class.php';

class RicercaScadenzeTemplate extends ScadenzeAbstract implements ScadenzePresentationInterface
{

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_SCADENZE_FORNITORE_TEMPLATE])) $_SESSION[self::RICERCA_SCADENZE_FORNITORE_TEMPLATE] = serialize(new RicercaScadenzeTemplate());
		return unserialize($_SESSION[self::RICERCA_SCADENZE_FORNITORE_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici()
	{
		$scadenzaFornitore = ScadenzaFornitore::getInstance();

		$esito = TRUE;
		$msg = "<br>";

		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($scadenzaFornitore->getDatScadenzaDa() == "") {
			$msg .= self::ERRORE_DATA_INIZIO_RICERCA;
			$esito = FALSE;
		}

		if ($scadenzaFornitore->getDatScadenzaA() == "") {
			$msg .= SELF::ERRORE_DATA_FINE_RICERCA;
			$esito = FALSE;
		}

		// ----------------------------------------------

		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}
		return $esito;
	}

	public function displayPagina()
	{
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_SCADENZE_FORNITORE;
		$risultato_ricerca = "";

		if ($scadenzaFornitore->getQtaScadenze() > 0)
		{
			$risultato_ricerca =
			"<table id='scadenze' class='display'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th>%ml.datscadenza%</th>" .
			"			<th>%ml.codforn%</th>" .
			"			<th>%ml.notascadenza%</th>" .
			"			<th>%ml.numfatt%</th>" .
			"			<th>%ml.tipaddebito%</th>" .
			"			<th>%ml.stascadenza%</th>" .
			"			<th>%ml.impscadenza%</th>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th></th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";

			$idfornitore_break = "";
			$datscadenza_break = "";
			$totale_fornitore = 0;
			$totale_scadenze = 0;

			foreach($scadenzaFornitore->getScadenze() as $row) {

				if (($idfornitore_break == self::EMPTYSTRING) && ($datscadenza_break == self::EMPTYSTRING)) {
					$idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
					$datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
					$desfornitore = trim($row[Fornitore::DES_FORNITORE]);
					$numfatt = trim($row[ScadenzaFornitore::NUM_FATTURA]);
					$datscadenza  = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
					$desfornitore2 = trim($row[Fornitore::DES_FORNITORE]);
					$datscadenza2  = trim($row[ScadenzaFornitore::DAT_SCADENZA_YYYYMMDD]);
				}

				if (trim($row[Registrazione::STA_REGISTRAZIONE]) == "00") {
					$class = "class=''";
					$bottoneModificaRegistrazione = self::MODIFICA_REGISTRAZIONE_HREF . trim($row[ScadenzaFornitore::ID_REGISTRAZIONE]) . self::MODIFICA_REGISTRAZIONE_ICON;
				}
				else {
					$class = "class=''";
					$bottoneModificaRegistrazione = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($row[ScadenzaFornitore::ID_REGISTRAZIONE]) . self::VISUALIZZA_REGISTRAZIONE_ICON;
				}

				if (trim($row[ScadenzaFornitore::NOTA_SCADENZA]) != self::EMPTYSTRING) {$notascadenza = trim($row[ScadenzaFornitore::NOTA_SCADENZA]);}
				else {$notascadenza = "&ndash;&ndash;&ndash;";}

				if (trim($row[ScadenzaFornitore::TIP_ADDEBITO]) != self::EMPTYSTRING) {$tipaddebito = trim($row[ScadenzaFornitore::TIP_ADDEBITO]);}
				else {$tipaddebito = self::CAMPO_VUOTO;}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_SOSPESA) {
					$stascadenza = self::CAMPO_VUOTO;
					$tdclass = self::DATA_KO;
					$bottoneModificaPagamento = self::EMPTYSTRING;
					$bottoneCancellaPagamento = self::EMPTYSTRING;
				}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_APERTA) {
					$stascadenza = self::SCADENZA_DA_PAGARE;
					$tdclass = self::DATA_KO;
					$bottoneModificaPagamento = self::EMPTYSTRING;
					$bottoneCancellaPagamento = self::EMPTYSTRING;
				}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
					$stascadenza = self::SCADENZA_PAGATA;
					$tdclass = self::DATA_OK;
					$bottoneModificaPagamento = self::MODIFICA_PAGAMENTO_HREF . trim($row[ScadenzaFornitore::ID_REGISTRAZIONE]) . "&idPagamento= " . trim($row[ScadenzaFornitore::ID_PAGAMENTO]) . self::MODIFICA_PAGAMENTO_ICON;
					$bottoneCancellaPagamento = self::CANCELLA_PAGAMENTO_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . "," . trim($row[ScadenzaFornitore::ID_PAGAMENTO]) . self::CANCELLA_PAGAMENTO_ICON;
				}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
					$stascadenza = self::SCADENZA_POSTICIPATA;
					$tdclass = self::DATA_CHIUSA;
					$bottoneModificaPagamento = self::MODIFICA_PAGAMENTO_HREF . trim($row[ScadenzaFornitore::ID_REGISTRAZIONE]) . "&idPagamento= " . trim($row[ScadenzaFornitore::ID_PAGAMENTO]) . self::MODIFICA_PAGAMENTO_ICON;
					$bottoneCancellaPagamento = self::CANCELLA_PAGAMENTO_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . "," . trim($row[ScadenzaFornitore::ID_PAGAMENTO]) . self::CANCELLA_PAGAMENTO_ICON;
				}

				$bottoneEstraiPdf = self::BOTTONE_ESTRAI_PDF;

				if ((trim($row[ScadenzaFornitore::ID_FORNITORE]) != $idfornitore_break) | (trim($row[ScadenzaFornitore::DAT_SCADENZA]) != $datscadenza_break)) {

					$risultato_ricerca .=
					"<tr class='dt-subtotale'>" .
					"	<td class='dt-center'>" . $datscadenza2 . "</td>" .
					"	<td>" . $desfornitore2 . "</td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td class='dt-right'><i>Totale data</i></td>" .
					"	<td></td>" .
					"	<td class='dt-right'>" . number_format($totale_fornitore, 2, ',', '.') . "</td>" .
					"	<td id='icons'></td>" .
					"	<td id='icons'></td>" .
					"	<td id='icons'></td>" .
					"</tr>";

					$desfornitore = trim($row[Fornitore::DES_FORNITORE]);
					$datscadenza  = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
					$numfatt = trim($row[ScadenzaFornitore::NUM_FATTURA]);
					$desfornitore2 = trim($row[Fornitore::DES_FORNITORE]);
					$datscadenza2  = trim($row[ScadenzaFornitore::DAT_SCADENZA_YYYYMMDD]);

					$idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
					$datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);

					$totale_scadenze += $totale_fornitore;
					$totale_fornitore = 0;
				}

				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td class='dt-center'>" . $datscadenza2 . "</td>" .
				"	<td>" . $desfornitore2 . "</td>" .
				"	<td class='dt-center'>" . $datscadenza . "</td>" .
				"	<td>" . $desfornitore . "</td>" .
				"	<td>" . $notascadenza . "</td>" .
				"	<td class='dt-center'>" . $numfatt . "</td>" .
				"	<td class='dt-center'>" . $tipaddebito . "</td>" .
				"	<td " . $tdclass . ">" . $stascadenza . "</td>" .
				"	<td class='dt-right'>" . number_format(trim($row['imp_in_scadenza']), 2, ',', '.') . "</td>" .
				"	<td id='icons'>" . $bottoneModificaRegistrazione . "</td>" .
				"	<td id='icons'>" . $bottoneModificaPagamento . "</td>" .
				"	<td id='icons'>" . $bottoneCancellaPagamento . "</td>" .
				"</tr>";

				$desfornitore = self::EMPTYSTRING;
				$datscadenza = self::EMPTYSTRING;
				$totale_fornitore += trim($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
			}

			$risultato_ricerca .=
			"<tr class='dt-subtotale'>" .
			"	<td class='dt-center'>" . $datscadenza2 . "</td>" .
			"	<td>" . $desfornitore2 . "</td>" .
			"	<td class='dt-center'>" . $datscadenza . "</td>" .
			"	<td>" . $desfornitore . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='dt-right'><i>Totale fornitore</i></td>" .
			"	<td></td>" .
			"	<td class='dt-right'>" . number_format($totale_fornitore, 2, ',', '.') . "</td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"</tr>";

			$totale_scadenze += $totale_fornitore;

			$risultato_ricerca .=
			"<tr class='dt-totale'>" .
			"	<td class='dt-center'>" . self::DATA_ALTA . "</td>" .
			"	<td></td>" .
			"	<td class='dt-center'>" . $datscadenza . "</td>" .
			"	<td>" . $desfornitore . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='dt-right'><i>Totale scadenze</i></td>" .
			"	<td></td>" .
			"	<td class='dt-right'>" . number_format($totale_scadenze, 2, ',', '.') . "</td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"</tr>";

			$risultato_ricerca .= "</tbody></table>";
		}
		else {
			$risultato_ricerca = self::EMPTYSTRING;
		}

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
				'%azione%' => $_SESSION[self::AZIONE],
				'%datascad_da%' => $scadenzaFornitore->getDatScadenzaDa(),
				'%datascad_a%' => $scadenzaFornitore->getDatScadenzaA(),
				'%codneg_sel%' => $scadenzaFornitore->getCodNegozioSel(),
				'%villa-selected%' => ($scadenzaFornitore->getCodNegozioSel() == self::VILLA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%brembate-selected%' => ($scadenzaFornitore->getCodNegozioSel() == self::BREMBATE) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%trezzo-selected%' => ($scadenzaFornitore->getCodNegozioSel() == self::TREZZO) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%00-selected%' => ($scadenzaFornitore->getStaScadenzaSel() == self::SCADENZA_APERTA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%10-selected%' => ($scadenzaFornitore->getStaScadenzaSel() == self::SCADENZA_CHIUSA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%02-selected%' => ($scadenzaFornitore->getStaScadenzaSel() == self::SCADENZA_RIMANDATA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%bottoneEstraiPdf%' => $bottoneEstraiPdf,
				'%risultato_ricerca%' => $risultato_ricerca
		);
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		}
	}
?>