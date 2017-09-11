<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.presentation.interface.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';
require_once 'sottoconto.class.php';

class RicercaRegistrazioneTemplate extends PrimanotaAbstract implements PrimanotaPresentationInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_REGISTRAZIONE_TEMPLATE])) $_SESSION[self::RICERCA_REGISTRAZIONE_TEMPLATE] = serialize(new RicercaRegistrazioneTemplate());
		return unserialize($_SESSION[self::RICERCA_REGISTRAZIONE_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici()
	{

		$registrazione = Registrazione::getInstance();
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
		$registrazione = Registrazione::getInstance();
		$causale = Causale::getInstance();
		$cliente = Cliente::getInstance();
		$fornitore = Fornitore::getInstance();

		$utility = Utility::getInstance();
		$db = Database::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_REGISTRAZIONE;
		$risultato_ricerca = "";

		if ($registrazione->getQtaRegistrazioni() > 0) {

			$risultato_ricerca =
			"<table id='registrazioni' class='display' width='100%'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th>%ml.datReg%</th>" .
			"			<th class='dt-left'>%ml.numfatt%</th>" .
			"			<th class='dt-left'>%ml.desReg%</th>" .
			"			<th class='dt-left'>%ml.codcau%</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";

			foreach($registrazione->getRegistrazioni() as $unaRegistrazione) {

				if (trim($unaRegistrazione[Registrazione::TIPO_RIGA_REGISTRAZIONE]) == Registrazione::RIGA_REGISTRAZIONE) {

					/**
					 * Imposto i bottoni validi sulla riga della registrazione
					 */

					switch ($unaRegistrazione[Registrazione::STA_REGISTRAZIONE]) {
						case (self::REGISTRAZIONE_APERTA): {

							$class = "class='dt-ok'";

							switch ($unaRegistrazione[Registrazione::COD_CAUSALE]) {
								case ($array[self::CORRISPETTIVO_MERCATO]): {
									$bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = self::MODIFICA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
									$bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
									break;
								}
								case ($array[self::CORRISPETTIVO_NEGOZIO]): {
									$bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = self::MODIFICA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
									$bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
									break;
								}
								case ($array[self::PAGAMENTO]): {
									$bottoneVisualizza = self::VISUALIZZA_PAGAMENTO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = "&nbsp;";
									$bottoneCancella = "&nbsp;";
									break;
								}
								case ($array[self::INCASSO]): {
									$bottoneVisualizza = self::VISUALIZZA_INCASSO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = "&nbsp;";
									$bottoneCancella = "&nbsp;";
									break;
								}
								default: {
									$bottoneVisualizza = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = self::MODIFICA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
									$bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
									break;
								}
							}
							break;
						}
						case (self::REGISTRAZIONE_ERRATA): {

							$class = "class='dt-ko'";

							switch ($row['cod_causale']) {
								case ($array[self::CORRISPETTIVO_MERCATO]): {
									$bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = self::MODIFICA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
									$bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
									break;
								}
								case ($array[self::CORRISPETTIVO_NEGOZIO]): {
									$bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = self::MODIFICA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
									$bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
									break;
								}
								case ($array[self::PAGAMENTO]): {
									$bottoneVisualizza = self::VISUALIZZA_PAGAMENTO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = "&nbsp;";
									$bottoneCancella = "&nbsp;";
									break;
								}
								case ($array[self::INCASSO]): {
									$bottoneVisualizza = self::VISUALIZZA_INCASSO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = "&nbsp;";
									$bottoneCancella = "&nbsp;";
									break;
								}
								default: {
									$bottoneVisualizza = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
									$bottoneModifica = self::MODIFICA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
									$bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
									break;
								}
							}
							break;
						}
						default: {
							$class = "class='dt-chiuso'";
							$bottoneVisualizza = "&nbsp;";
							$bottoneModifica = "&nbsp;";
							$bottoneCancella = "&nbsp;";
							break;
						}
					}

					$risultato_ricerca .=
					"<tr " . $class . ">" .
					"	<td>" . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[Registrazione::DAT_REGISTRAZIONE_YYYYMMDD]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[Registrazione::DAT_REGISTRAZIONE]) . "</td>" .
					"	<td class='td-left'>" . trim($unaRegistrazione[Registrazione::NUM_FATTURA]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[Registrazione::DES_REGISTRAZIONE]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[Registrazione::COD_CAUSALE]) . " &ndash; " . trim($unaRegistrazione[Causale::DES_CAUSALE]) . "</td>" .
					"	<td id='icons'>" . $bottoneVisualizza . "</td>" .
					"	<td id='icons'>" . $bottoneModifica . "</td>" .
					"	<td id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";

				}
				elseif (trim($unaRegistrazione[Registrazione::TIPO_RIGA_REGISTRAZIONE]) == Registrazione::RIGA_DETTAGLIO_REGISTRAZIONE) {

					$risultato_ricerca .=
					"<tr>" .
					"	<td>" . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[Registrazione::DAT_REGISTRAZIONE_YYYYMMDD]) . "</td>" .
					"	<td>" . trim($unaRegistrazione[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]) . "</td>" .
					"	<td class='dt-right'>" . trim($unaRegistrazione[DettaglioRegistrazione::IND_DAREAVERE]) . "</td>" .
					"	<td class='dt-right'>" . trim($unaRegistrazione[DettaglioRegistrazione::IMP_REGISTRAZIONE]) .  "</td>" .
					"	<td><i>" . trim($unaRegistrazione[DettaglioRegistrazione::COD_CONTO]) . trim($unaRegistrazione[DettaglioRegistrazione::COD_SOTTOCONTO]) . " &ndash; " . trim($unaRegistrazione[Sottoconto::DES_SOTTOCONTO]) . "</i></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"	<td></td>" .
					"</tr>";

				}
			}
			$risultato_ricerca .= "</tbody></table>";
		}

		$causale->setCodCausale("");
		$elencoCausali = $causale->caricaCausali($db);

		$fornitore->load($db);
		$cliente->load($db);
		$_SESSION[self::FORNITORE] = serialize($fornitore);
		$_SESSION[self::CLIENTE] = serialize($cliente);

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
				'%azione%' => $_SESSION[self::AZIONE],
				'%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
				'%datareg_da%' => $registrazione->getDatRegistrazioneDa(),
				'%datareg_a%' => $registrazione->getDatRegistrazioneA(),
				'%villa-selected%' => ($registrazione->getCodNegozioSel() == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($registrazione->getCodNegozioSel() == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($registrazione->getCodNegozioSel() == "TRE") ? "selected" : "",
				'%elenco_causali%' => $elencoCausali,
				'%elenco_causali_cre%' => $elencoCausali,
				'%elenco_causali_inc_cre%' => $elencoCausali,
				'%elenco_causali_pag_cre%' => $elencoCausali,
				'%elenco_causali_cormer_cre%' => $elencoCausali,
				'%elenco_fornitori%' => $this->caricaElencoFornitori($fornitore),
				'%elenco_clienti%' => $this->caricaElencoClienti($cliente),
				'%risultato_ricerca%' => $risultato_ricerca
		);
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>