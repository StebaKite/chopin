<?php

require_once 'nexus6.main.interface.php';

interface ScadenzePresentationInterface extends MainNexus6Interface {

	// Nomi

	const SCADENZE_FORNITORE = "scadenzeFornitoreTrovate";

	// Pagine

	const PAGINA_RICERCA_SCADENZE_FORNITORE = "/scadenze/ricercaScadenze.form.html";

	// Bottoni

	const MODIFICA_REGISTRAZIONE_HREF = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=";
	const MODIFICA_REGISTRAZIONE_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.modificaFattura%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
	const VISUALIZZA_REGISTRAZIONE_HREF = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_REGISTRAZIONE_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.visualizzaFattura%'><span class='ui-icon ui-icon-search'></span></li></a>";
	const MODIFICA_PAGAMENTO_HREF = "<a class='tooltip' href='../primanota/modificaPagamentoFacade.class.php?modo=start&idRegistrazione=";
	const MODIFICA_PAGAMENTO_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.visualizzaPagamento%'><span class='ui-icon ui-icon-link'></span></li></a>";
	const CANCELLA_PAGAMENTO_HREF = "<a class='tooltip' onclick='cancellaPagamento(";
	const CANCELLA_PAGAMENTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancellaPagamento%'><span class='ui-icon ui-icon-scissors'></span></li></a>";

	// Errori e messaggi

	const DATA_KO = "class='dt-ko'";
	const DATA_OK = "class='dt-ok'";
	const DATA_CHIUSA = "class='dt-chiuso'";
	const SCADENZA_DA_PAGARE = "Da Pagare";
	const SCADENZA_PAGATA = "Pagato";
	const SCADENZA_POSTICIPATA = "Posticipata";
	const SCADENZA_APERTA = "00";
	const SCADENZA_CHIUSA = "10";
	const SCADENZA_SOSPESA = "  ";
	const SCADENZA_RIMANDATA = "02";
	const ERRORE_DATA_INIZIO_RICERCA  = "<br>&ndash; Manca la data di inizio ricerca";
	const ERRORE_DATA_FINE_RICERCA  = "<br>&ndash; Manca la data di fine ricerca";

	// Oggetti

	const RICERCA_SCADENZE_FORNITORE_TEMPLATE = "Obj_ricercascadenzefornitoretemplate";

	// Metodi

	public function getInstance();
	public function inizializzaPagina();
	public function controlliLogici();
	public function displayPagina();
}

?>