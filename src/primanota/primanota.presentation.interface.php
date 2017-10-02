<?php

require_once 'nexus6.main.interface.php';

interface PrimanotaPresentationInterface extends MainNexus6Interface {

	// Nomi

	const REGISTRAZIONE_APERTA = "00";
	const REGISTRAZIONE_ERRATA = "02";

	const CORRISPETTIVO_MERCATO = "corrispettiviMercato";
	const CORRISPETTIVO_NEGOZIO = "corrispettivoNegozio";
	const PAGAMENTO = 'pagamentoFornitori';
	const INCASSO = 'incassoFattureClienti';

	// Pagine

	const PAGINA_RICERCA_REGISTRAZIONE = "/primanota/ricercaRegistrazione.form.html";

	// Bottoni

	const VISUALIZZA_CORRISPETTIVO_MERCATO_HREF = "<a class='tooltip' href='../primanota/visualizzaCorrispettivoFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_CORRISPETTIVO_NEGOZIO_HREF = "<a class='tooltip' href='../primanota/visualizzaCorrispettivoNegozioFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_PAGAMENTO_HREF = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_INCASSO_HREF = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_REGISTRAZIONE_HREF = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=";

	const MODIFICA_CORRISPETTIVO_MERCATO_HREF = "<a class='tooltip' href='../primanota/modificaCorrispettivoMercatoFacade.class.php?modo=start&idRegistrazione=";
	const MODIFICA_CORRISPETTIVO_NEGOZIO_HREF = "<a class='tooltip' href='../primanota/modificaCorrispettivoNegozioFacade.class.php?modo=start&idRegistrazione=";
	const MODIFICA_REGISTRAZIONE_HREF = "<a class='tooltip' onclick='modificaRegistrazione(";
	const MODIFICA_PAGAMENTO_HREF = "<a class='tooltip' onclick='modificaPagamento(";
	
	const CANCELLA_REGISTRAZIONE_HREF = "<a class='tooltip' onclick='cancellaRegistrazione(";

	const VISUALIZZA_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.visualizza%'><span class='ui-icon ui-icon-search'></span></li></a>";
	const MODIFICA_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
	const CANCELLA_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

	// Actions


	// Errori e messaggi

	const ERRORE_DATA_DA = "ERRORE: manca la data di inizio ricerca";
	const ERRORE_DATA_A = "ERRORE: manca la data di fine ricerca";

	// Oggetti

	const RICERCA_REGISTRAZIONE_TEMPLATE = "Obj_ricercaregistrazionetemplate";
	const FORNITORE = "Obj_fornitore";
	const CLIENTE = "Obj_cliente";

	// Metodi

	public function getInstance();
	public function inizializzaPagina();
	public function controlliLogici();
	public function displayPagina();
}

?>
