<?php

require_once 'nexus6.main.interface.php';

interface ConfigurazioniPresentationInterface extends MainNexus6Interface {

	// Nomi

	const CONTI = "contiTrovati";
	const QTA_CONTI = "numContiTrovati";
	const TITOLO = "titoloPagina";
	const NUM_REG_CONTO = "tot_registrazioni_conto";

	const CONTO_ECONOMICO = "Conto Economico";
	const STATO_PATRIMONIALE = "Stato Patrimoniale";
	const DARE = "Dare";
	const AVERE = "Avere";
	const NUM_CONTI_CAUSALE = "tot_conti_causale";
	const NUM_REG_CAUSALE = "tot_registrazioni_causale";

	// Pagine

	const PAGINA_RICERCA_CONTO = "/configurazioni/ricercaConto.form.html";
	const PAGINA_MODIFICA_CONTO = "/configurazioni/modificaConto.form.html";
	const PAGINA_GENERA_MASTRINO = "/configurazioni/generaMastrinoConto.form.html";
	const PAGINA_CREA_CONTO = "/configurazioni/creaConto.form.html";
	const PAGINA_RICERCA_CAUSALE = "/configurazioni/ricercaCausale.form.html";
	const PAGINA_CREA_CAUSALE = "/configurazioni/creaCausale.form.html";
	const PAGINA_RICERCA_PROGRESSIVO_FATTURA = "/configurazioni/ricercaProgressivoFattura.form.html";
	const PAGINA_AGGIORNA_PROGRESSIVO_FATTURA = "/configurazioni/modificaProgressivoFattura.form.html";
	const PAGINA_CONFIGURA_CAUSALE = "/configurazioni/configuraCausale.form.html";
	const PAGINA_MODIFICA_CAUSALE = "/configurazioni/modificaCausale.form.html";

	// Bottoni

 	const MODIFICA_CONTO_HREF = "<a class='tooltip' onclick='modificaConto(";
 	const MODIFICA_CONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
 	const CANCELLA_CONTO_HREF = "<a class='tooltip' onclick='cancellaConto(";
 	const CANCELLA_CONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
 	const GENERA_MASTRINO_HREF = "<a class='tooltip' onclick='generaMastrino(";
 	const GENERA_MASTRINO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.mastrino%'><span class='ui-icon ui-icon-document'></span></li></a>";
	const ESTRAI_PDF = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";

	const MODIFICA_CAUSALE_HREF = "<a class='tooltip' onclick='modificaCausale(";
	const MODIFICA_CAUSALE_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
	const CONFIGURA_CAUSALE_HREF = "<a class='tooltip' onclick='configuraCausale(";
	const CONFIGURA_CAUSALE_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.configura%'><span class='ui-icon ui-icon-wrench'></span></li></a>";
	const CANCELLA_CAUSALE_HREF = "<a class='tooltip' onclick='cancellaCausale(";
	const CANCELLA_CAUSALE_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

	const MODIFICA_PROGRESSIVO_HREF = "<a class='tooltip' onclick='modificaProgressivoFattura(";
	const MODIFICA_PROGRESSIVO_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";

	// Actions

	const AZIONE_RICERCA_CONTO = "../configurazioni/ricercaContoFacade.class.php?modo=go";

	// Errori e messaggi

	const ERRORE_CODICE_CONTO				= "<br>&ndash; Manca il codice del conto";
	const ERRORE_CODICE_CONTO_NUMERICO		= "<br>&ndash; Il codice conto deve essere numerico";
	const ERRORE_CODICE_CONTO_INVALIDO		= "<br>&ndash; Il codice conto deve essere maggiore di 100";
 	const ERRORE_DESCRIZIONE_CONTO			= "<br>&ndash; Manca la descrizione del conto";
 	const ERRORE_ASSENZA_SOTTOCONTI			= "<br>&ndash; Mancano i sottoconti";
 	const ERRORE_CODICE_CAUSALE				= "<br>&ndash; Manca il codice della causale";
 	const ERRORE_CODICE_CAUSALE_NUMERICO	= "<br>&ndash; Il codice causale deve essere numerico";
 	const ERRORE_CODICE_CAUSALE_INVALIDO	= "<br>&ndash; Il codice causale deve essere maggiore di 1000";
 	const ERRORE_DESCRIZIONE_CAUSALE		= "<br>&ndash; Manca la descrizione della causale";

	// Oggetti

	const RICERCA_CONTO_TEMPLATE    			= "Obj_ricercacontotemplate";
	const CREA_CONTO_TEMPLATE					= "Obj_creacontotemplate";
	const MODIFICA_CONTO_TEMPLATE				= "Obj_modificacontotemplate";
	const GENERA_MASTRINO_TEMPLATE				= "Obj_generamastrinotemplate";
	const RICERCA_CAUSALI_TEMPLATE				= "Obj_ricercacausalitemplate";
	const CREA_CAUSALE_TEMPLATE					= "Obj_creacausaletemplate";
	const RICERCA_PROGRESSIVO_FATTURA_TEMPLATE	= "Obj_ricercaprogressivofatturatemplate";
	const AGGIORNA_PROGRESSIVO_FATTURA_TEMPLATE = "Obj_aggiornaprogressivofatturatemplate";
	const CONFIGURA_CAUSALE_TEMPLATE 			= "Obj_configuracausaletemplate";
	const MODIFICA_CAUSALE_TEMPLATE				= "Obj_modificacausaletemplate";

	// Metodi

	public function getInstance();
	public function inizializzaPagina();
	public function controlliLogici();
	public function displayPagina();
}

?>
