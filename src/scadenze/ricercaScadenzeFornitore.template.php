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
		
		// ----------------------------------------------
		

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

		if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0)
		{
			$risultato_ricerca =
			"<div class='row'>" .
			"    <div class='col-sm-4'>" .
			"        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
			"    </div>" .
			"    <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
			"</div>" .
			"<br/>" .
			"<table class='table table-bordered table-hover'>" .
			"	<thead>" .
			"		<tr>" .
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
			"	<tbody id='myTable'>";			

			$idfornitore_break = "";
			$datscadenza_break = "";
			$totale_fornitore = 0;
			$totale_scadenze = 0;

			foreach($scadenzaFornitore->getScadenzeDaPagare() as $row) {

				if (($idfornitore_break == self::EMPTYSTRING) && ($datscadenza_break == self::EMPTYSTRING)) {
					$idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
					$datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
					$desfornitore = trim($row[Fornitore::DES_FORNITORE]);
					$numfatt = trim($row[ScadenzaFornitore::NUM_FATTURA]);
					$datscadenza  = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
					$desfornitore2 = trim($row[Fornitore::DES_FORNITORE]);
					$datscadenza2  = trim($row[ScadenzaFornitore::DAT_SCADENZA_YYYYMMDD]);
				}

				
				
// 				if (trim($row[Registrazione::STA_REGISTRAZIONE]) == "00") {
// 					$bottoneModificaRegistrazione = self::MODIFICA_REGISTRAZIONE_HREF . trim($row[ScadenzaFornitore::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
// 				}
// 				else {
// 					$bottoneModificaRegistrazione = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($row[ScadenzaFornitore::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
// 				}

				
				
				if (trim($row[ScadenzaFornitore::NOTA_SCADENZA]) != self::EMPTYSTRING) {$notascadenza = trim($row[ScadenzaFornitore::NOTA_SCADENZA]);}
				else {$notascadenza = "&ndash;&ndash;&ndash;";}

				if (trim($row[ScadenzaFornitore::TIP_ADDEBITO]) != self::EMPTYSTRING) {$tipaddebito = trim($row[ScadenzaFornitore::TIP_ADDEBITO]);}
				else {$tipaddebito = self::CAMPO_VUOTO;}

				
				$bottoneVisualizzaScadenza = self::VISUALIZZA_SCADENZA_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . self::VISUALIZZA_ICON;
				$bottoneModificaScadenza = self::MODIFICA_SCADENZA_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . self::MODIFICA_ICON;
				$bottoneCancellaScadenza = self::CANCELLA_SCADENZA_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . self::CANCELLA_ICON;
				
				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_SOSPESA) {
					$stascadenza = self::CAMPO_VUOTO;
					$tdclass = self::DATA_KO;
				}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_APERTA) {
					$stascadenza = self::SCADENZA_DA_PAGARE;
					$tdclass = self::DATA_KO;
				}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
					$stascadenza = self::SCADENZA_PAGATA;
					$tdclass = self::DATA_OK;
				}

				if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
					$stascadenza = self::SCADENZA_POSTICIPATA;
					$tdclass = self::DATA_CHIUSA;
				}

				if ((trim($row[ScadenzaFornitore::ID_FORNITORE]) != $idfornitore_break) | (trim($row[ScadenzaFornitore::DAT_SCADENZA]) != $datscadenza_break)) {

					$risultato_ricerca .=
					"<tr>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td class='bg-info'><strong>Totale data</strong></td>" .
					"	<td class='bg-info'></td>" .
					"	<td class='bg-info'><strong>" . number_format($totale_fornitore, 2, ',', '.') . "</strong></td>" .
					"	<td class='bg-info'></td>" .
					"	<td class='bg-info'></td>" .
					"	<td class='bg-info'></td>" .
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
				"	<td>" . $datscadenza . "</td>" .
				"	<td>" . $desfornitore . "</td>" .
				"	<td>" . $notascadenza . "</td>" .
				"	<td>" . $numfatt . "</td>" .
				"	<td>" . $tipaddebito . "</td>" .
				"	<td " . $tdclass . ">" . $stascadenza . "</td>" .
				"	<td>" . number_format(trim($row['imp_in_scadenza']), 2, ',', '.') . "</td>" .
				"	<td>" . $bottoneVisualizzaScadenza . "</td>" .
				"	<td>" . $bottoneModificaScadenza . "</td>" .
				"	<td>" . $bottoneCancellaScadenza . "</td>" .
				"</tr>";

				$desfornitore = self::EMPTYSTRING;
				$datscadenza = self::EMPTYSTRING;
				$totale_fornitore += trim($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
			}

			$risultato_ricerca .=
			"<tr>" .
			"	<td>" . $datscadenza . "</td>" .
			"	<td>" . $desfornitore . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='bg-info'><strong>Totale fornitore</strong></td>" .
			"	<td class='bg-info'></td>" .
			"	<td class='bg-info'><strong>" . number_format($totale_fornitore, 2, ',', '.') . "</strong></td>" .
			"	<td class='bg-info'></td>" .
			"	<td class='bg-info'></td>" .
			"	<td class='bg-info'></td>" .
			"</tr>";

			$totale_scadenze += $totale_fornitore;

			$risultato_ricerca .=
			"<tr>" .
			"	<td>" . $datscadenza . "</td>" .
			"	<td>" . $desfornitore . "</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='bg-info'><strong>Totale scadenze</strong></td>" .
			"	<td class='bg-info'></td>" .
			"	<td class='bg-info'><strong>" . number_format($totale_scadenze, 2, ',', '.') . "</strong></td>" .
			"	<td class='bg-info'></td>" .
			"	<td class='bg-info'></td>" .
			"	<td class='bg-info'></td>" .
			"</tr>";

			$risultato_ricerca .= "</tbody></table>";
		}
		else {
			$risultato_ricerca =
			"<div class='row'>" .
			"    <div class='col-sm-12'>" . $_SESSION[self::MSG] . "</div>" .
			"</div>";
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
				'%risultato_ricerca%' => $risultato_ricerca
		);
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		}
	}
?>