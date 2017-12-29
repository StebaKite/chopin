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

	// Files XML

	const XML_MODIFICA_REGISTRAZIONE = "/primanota/xml/modificaRegistrazione.xml";
	const XML_VISUALIZZA_REGISTRAZIONE = "/primanota/xml/visualizzaRegistrazione.xml";
	const XML_SCADENZE_CLIENTE_APERTE = "/primanota/xml/scadenzeClienteAperte.xml";
	const XML_VISUALIZZA_INCASSO = "/primanota/xml/visualizzaIncasso.xml";
	const XML_MODIFICA_INCASSO = "/primanota/xml/modificaIncasso.xml";
	
	// Bottoni

	const VISUALIZZA_CORRISPETTIVO_MERCATO_HREF = "<a href='../primanota/visualizzaCorrispettivoFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_CORRISPETTIVO_NEGOZIO_HREF = "<a href='../primanota/visualizzaCorrispettivoNegozioFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_PAGAMENTO_HREF = "<a href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=";
	const VISUALIZZA_INCASSO_HREF = "<a onclick='visualizzaIncasso(";
	const VISUALIZZA_REGISTRAZIONE_HREF = "<a onclick='visualizzaRegistrazione(";

	const MODIFICA_CORRISPETTIVO_MERCATO_HREF = "<a href='../primanota/modificaCorrispettivoMercatoFacade.class.php?modo=start&idRegistrazione=";
	const MODIFICA_CORRISPETTIVO_NEGOZIO_HREF = "<a href='../primanota/modificaCorrispettivoNegozioFacade.class.php?modo=start&idRegistrazione=";
	const MODIFICA_REGISTRAZIONE_HREF = "<a onclick='modificaRegistrazione(";
	const MODIFICA_PAGAMENTO_HREF = "<a onclick='modificaPagamento(";
	const MODIFICA_INCASSO_HREF = "<a onclick='modificaIncasso(";
	
	const CANCELLA_REGISTRAZIONE_HREF = "<a onclick='cancellaRegistrazione(";
	
	const VISUALIZZA_ICON = ")'><span class='glyphicon glyphicon-eye-open'></span></a>";
	const MODIFICA_ICON = ")'><span class='glyphicon glyphicon-edit'></span></a>";
	const CANCELLA_ICON = ")'><span class='glyphicon glyphicon-trash'></span></a>";
	const OK_ICON = "<span class='glyphicon glyphicon-ok'></span>";

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
