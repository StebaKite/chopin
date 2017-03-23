<?php

require_once 'nexus6.main.interface.php';

interface ConfigurazioniPresentationInterface extends MainNexus6Interface {

	// Nomi

	const CONTI = "contiTrovati";
	const QTA_CONTI = "numContiTrovati";
	const TITOLO = "titoloPagina";
	const NUM_REG_SOTTOCONTO = "totale_registrazioni_sottoconto";
	const NUM_REG_CONTO = "tot_registrazioni_conto";
	const NESSUNO = "NS";
	const COSTI_FISSI = "CF";
	const COSTI_VARIABILI = "CV";
	const RICAVI = "RC";
	const CONTO_ECONOMICO = "Conto Economico";
	const STATO_PATRIMONIALE = "Stato Patrimoniale";
	const DARE = "Dare";
	const AVERE = "Avere";

	// Pagine

	const PAGINA_RICERCA_CONTO    	= "/configurazioni/ricercaConto.form.html";
	const PAGINA_MODIFICA_CONTO		= "/configurazioni/modificaConto.form.html";
	const PAGINA_GENERA_MASTRINO	= "/configurazioni/generaMastrinoConto.form.html";

	// Bottoni

 	const MODIFICA_CONTO_HREF = "<a class='tooltip' href='../configurazioni/modificaContoFacade.class.php?modo=start&codconto=";
 	const MODIFICA_CONTO_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
 	const CANCELLA_CONTO_HREF = "<a class='tooltip' onclick='cancellaConto(";
 	const CANCELLA_CONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
 	const CANCELLA_SOTTOCONTO_HREF = "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottoconto(";
 	const CANCELLA_SOTTOCONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
 	const MODIFICA_GRUPPO_SOTTOCONTO_HREF = "<td id='icons'><a class='tooltip' onclick='modificaGruppoSottoconto(";
 	const MODIFICA_GRUPPO_SOTTOCONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='Cambia gruppo'><span class='ui-icon ui-icon-tag'></span></li></a></td>";
 	const GENERA_MASTRINO_HREF = "<a class='tooltip' onclick='generaMastrino(";
 	const GENERA_MASTRINO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.mastrino%'><span class='ui-icon ui-icon-document'></span></li></a>";
	const ESTRAI_PDF = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
	
 	
	// Actions

	const AZIONE_RICERCA_CONTO = "../configurazioni/ricercaContoFacade.class.php?modo=go";

	// Errori e messaggi

 	const ERRORE_DESCRIZIONE_CONTO		= "<br>&ndash; Manca la descrizione del conto";
 	const ERRORE_ASSENZA_SOTTOCONTI		= "<br>&ndash; Mancano i sottoconti";

	// Oggetti

	const RICERCA_CONTO_TEMPLATE    = "Obj_ricercacontotemplate";
	const MODIFICA_CONTO_TEMPLATE	= "Obj_modificacontotemplate";
	const GENERA_MASTRINO_TEMPLATE	= "Obj_generamastrinotemplate";
	
	// Metodi

	public function getInstance();
	public function inizializzaPagina();
	public function controlliLogici();
	public function displayPagina();
}

?>
