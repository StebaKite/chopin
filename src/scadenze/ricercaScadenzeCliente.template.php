<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.presentation.interface.php';
require_once 'utility.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';
require_once 'registrazione.class.php';

class RicercaScadenzeClienteTemplate extends ScadenzeAbstract implements ScadenzePresentationInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_SCADENZE_CLIENTE_TEMPLATE])) $_SESSION[self::RICERCA_SCADENZE_CLIENTE_TEMPLATE] = serialize(new RicercaScadenzeClienteTemplate());
		return unserialize($_SESSION[self::RICERCA_SCADENZE_CLIENTE_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici()
	{
		$scadenzaCliente = ScadenzaCliente::getInstance();

		$esito = TRUE;
		$msg = "<br>";

		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($scadenzaCliente->getDatScadenzaDa() == "") {
			$msg .= self::ERRORE_DATA_INIZIO_RICERCA;
			$esito = FALSE;
		}

		if ($scadenzaCliente->getDatScadenzaA() == "") {
			$msg .= SELF::ERRORE_DATA_FINE_RICERCA;
			$esito = FALSE;
		}

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
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_SCADENZE_CLIENTE;
		$risultato_ricerca = "";

		if ($scadenzaCliente->getQtaScadenze() > 0)
		{
			$risultato_ricerca =
			"<table id='scadenze' class='display'>" .
			"	<thead>" .
			"		<th></th>" .
			"		<th></th>" .
			"		<th>%ml.datregistrazione%</th>" .
			"		<th>%ml.codcliente%</th>" .
			"		<th>%ml.notascadenza%</th>" .
			"		<th>%ml.numfatt%</th>" .
			"		<th>%ml.tipaddebito%</th>" .
			"		<th>%ml.stascadenza%</th>" .
			"		<th>%ml.impscadenza%</th>" .
			"		<th></th>" .
			"		<th></th>" .
			"		<th></th>" .
			"	</thead>" .
			"	<tbody>";

			$idcliente_break = "";
			$datregistrazione_break = "";
			$totale_cliente = 0;
			$totale_scadenze = 0;

			foreach($scadenzaCliente->getScadenze() as $row) {

				if (($idcliente_break == "") && ($datregistrazione_break == "")) {
					$idcliente_break = trim($row[ScadenzaCliente::ID_CLIENTE]);
					$datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
					$descliente = trim($row[Cliente::DES_CLIENTE]);
					$datregistrazione  = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
					$descliente2 = trim($row[Cliente::DES_CLIENTE]);
					$datregistrazione2  = trim($row[Registrazione::DAT_REGISTRAZIONE_YYYYMMDD]);
				}

				if (trim($row[Registrazione::STA_REGISTRAZIONE]) == self::REGISTRAZIONE_APERTA) {
					$class = "class=''";
					$bottoneModificaRegistrazione = self::MODIFICA_REGISTRAZIONE_HREF . trim($row[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_REGISTRAZIONE_ICON;
				}
				else {
					$class = "class=''";
					$bottoneModificaRegistrazione = self::MODIFICA_REGISTRAZIONE_HREF . trim($row[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_REGISTRAZIONE_ICON;
					$bottoneModificaRegistrazione = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($row[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_REGISTRAZIONE_ICON;
				}

				if (trim($row[ScadenzaCliente::NOTA]) != "") {$nota = trim($row[ScadenzaCliente::NOTA]);}
				else {$nota = "&ndash;&ndash;&ndash;";}

				if (trim($row[ScadenzaCliente::TIP_ADDEBITO]) != "") {$tipaddebito = trim($row[ScadenzaCliente::TIP_ADDEBITO]);}
				else {$tipaddebito = "&ndash;&ndash;&ndash;";}

				if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_APERTA) {
					$stascadenza = "Da Incassare";
					$tdclass = "class='dt-ko'";
					$bottoneModificaIncasso = "";
					$bottoneCancellaIncasso = "";
				}

				if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
					$stascadenza = "Incassato";
					$tdclass = "class='dt-ok'";
					$bottoneModificaIncasso = self::MODIFICA_INCASSO_HREF . trim($row[Registrazione::ID_REGISTRAZIONE]) . "&idIncasso=" . trim($row[ScadenzaCliente::ID_INCASSO]) . self::MODIFICA_INCASSO_ICON;
					$bottoneCancellaIncasso = self::CANCELLA_INCASSO_HREF . trim($row[ScadenzaCliente::ID_SCADENZA]) . "," . trim($row[ScadenzaCliente::ID_INCASSO]) . self::CANCELLA_INCASSO_ICON ;
				}

				if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
					$stascadenza = "Posticipato";
					$tdclass = "class='dt-chiuso'";
					$bottoneModificaIncasso = self::MODIFICA_INCASSO_HREF . trim($row[Registrazione::ID_REGISTRAZIONE]) . "&idIncasso=" . trim($row[ScadenzaCliente::ID_INCASSO]) . self::MODIFICA_INCASSO_ICON;
					$bottoneCancellaIncasso = self::CANCELLA_INCASSO_HREF . trim($row[ScadenzaCliente::ID_SCADENZA]) . "," . trim($row[ScadenzaCliente::ID_INCASSO]) . self::CANCELLA_INCASSO_ICON ;
				}

				$numfatt = trim($row[ScadenzaCliente::NUM_FATTURA]);
				$bottoneEstraiPdf = self::BOTTONE_ESTRAI_PDF;

				if ((trim($row[ScadenzaCliente::ID_CLIENTE]) != $idcliente_break) | (trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]) != $datregistrazione_break)) {

					$risultato_ricerca .=
					"<tr class='dt-subtotale'>" .
					"	<td class='dt-center'>" . $datregistrazione2 . "</td>" .
					"	<td>" . $descliente2 . "</td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td class='dt-right'><i>Totale data</i></td>" .
					"	<td></td>" .
					"	<td class='dt-right'>" . number_format($totale_cliente, 2, ',', '.') . "</td>" .
					"	<td id='icons'></td>" .
					"	<td id='icons'></td>" .
					"	<td id='icons'></td>" .
					"</tr>";

					$descliente = trim($row[Cliente::DES_CLIENTE]);
					$datregistrazione  = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
					$descliente2 = trim($row[Cliente::DES_CLIENTE]);
					$datregistrazione2  = trim($row[Registrazione::DAT_REGISTRAZIONE_YYYYMMDD]);

					$idcliente_break = trim($row[Cliente::ID_CLIENTE]);
					$datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);

					$totale_scadenze += $totale_cliente;
					$totale_cliente = 0;
				}

				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td class='dt-center'>" . $datregistrazione2 . "</td>" .
				"	<td>" . $descliente2 . "</td>" .
				"	<td class='dt-center'>" . $datregistrazione . "</td>" .
				"	<td>" . $descliente . "</td>" .
				"	<td>" . $nota . "</td>" .
				"	<td class='dt-center'>" . $numfatt . "</td>" .
				"	<td class='dt-center'>" . $tipaddebito . "</td>" .
				"	<td " . $tdclass . ">" . $stascadenza . "</td>" .
				"	<td class='dt-right'>" . number_format(trim($row[ScadenzaCliente::IMP_REGISTRAZIONE]), 2, ',', '.') . "</td>" .
				"	<td id='icons'>" . $bottoneModificaRegistrazione . "</td>" .
				"	<td id='icons'>" . $bottoneModificaIncasso . "</td>" .
				"	<td id='icons'>" . $bottoneCancellaIncasso . "</td>" .
				"</tr>";

				$descliente = "";
				$datregistrazione = "";
				$totale_cliente += trim($row[ScadenzaCliente::IMP_REGISTRAZIONE]);
			}

			$risultato_ricerca = $risultato_ricerca .
			"<tr class='dt-subtotale'>" .
			"	<td class='dt-center'>" . $datregistrazione2 . "</td>" .
			"	<td>" . $descliente2 . "</td>" .
			"	<td class='dt-center'>" . $datregistrazione . "</td>" .
			"	<td>" . $descliente . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='dt-right'><i>Totale cliente</i></td>" .
			"	<td></td>" .
			"	<td class='dt-right'>" . number_format($totale_cliente, 2, ',', '.') . "</td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"	<td id='icons'></td>" .
			"</tr>";

			$totale_scadenze += $totale_cliente;

			$risultato_ricerca = $risultato_ricerca .
			"<tr class='dt-totale'>" .
			"	<td class='dt-center'>31/12/9999</td>" .
			"	<td></td>" .
			"	<td class='dt-center'>" . $datregistrazione . "</td>" .
			"	<td>" . $descliente . "</td>" .
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
				'%datascad_da%' => $scadenzaCliente->getDatScadenzaDa(),
				'%datascad_a%' => $scadenzaCliente->getDatScadenzaA(),
				'%codneg_sel%' => $scadenzaCliente->getCodNegozioSel(),
				'%villa-selected%' => ($scadenzaCliente->getCodNegozioSel() == self::VILLA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%brembate-selected%' => ($scadenzaCliente->getCodNegozioSel() == self::BREMBATE) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%trezzo-selected%' => ($scadenzaCliente->getCodNegozioSel() == self::TREZZO) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%00-selected%' => ($scadenzaCliente->getStaScadenzaSel() == self::SCADENZA_APERTA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%10-selected%' => ($scadenzaCliente->getStaScadenzaSel() == self::SCADENZA_CHIUSA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%02-selected%' => ($scadenzaCliente->getStaScadenzaSel() == self::SCADENZA_RIMANDATA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%bottoneEstraiPdf%' => $bottoneEstraiPdf,
				'%risultato_ricerca%' => $risultato_ricerca
		);
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>