<?php

require_once 'nexus6.main.interface.php';

interface AnagraficaPresentationInterface extends MainNexus6Interface {

	// Nomi

	const CLIENTI = "clientiTrovati";
	const FORNITORI = "fornitoriTrovati";
	const QTA_CLIENTI = "numClientiTrovati";
	const QTA_FORNITORI = "numFornitoriTrovati";

	const CATEGORIE_CLIENTE = "elenco_categorie_cliente";
	const TITOLO = "titoloPagina";
	
// 	const CODICE_CLIENTE = "cod_cliente";
// 	const DESCRIZIONE_CLIENTE = "des_cliente";
// 	const INDIRIZZO_CLIENTE = "des_indirizzo_cliente";
// 	const CITTA_CLIENTE = "des_citta_cliente";
// 	const CAP_CLIENTE = "cap_cliente";

	const QTA_REGISTRAZIONI_FORNITORE = "tot_registrazioni_fornitore";
	
// 	const CODICE_FORNITORE = "cod_fornitore";
// 	const DESCRIZIONE_FORNITORE = "des_fornitore";
// 	const INDIRIZZO_FORNITORE = "des_indirizzo_fornitore";
// 	const CITTA_FORNITORE = "des_citta_fornitore";
// 	const CAP_FORNITORE = "cap_fornitore";
// 	const GIORNI_SCADENZA_FATTURA = "num_gg_scadenza_fattura";

	// Tips

	const TIP_CONFERMA = "confermaTip";

	// Pagine

	const PAGINA_RICERCA_CLIENTE   = "/anagrafica/ricercaCliente.form.html";
	const PAGINA_RICERCA_FORNITORE = "/anagrafica/ricercaFornitore.form.html";
	const PAGINA_CREA_FORNITORE    = "/anagrafica/creaFornitore.form.html";
	const PAGINA_CREA_CLIENTE      = "/anagrafica/creaCliente.form.html";

	// Bottoni

	const MODIFICA_CLIENTE_HREF = "<a class='tooltip' href='../anagrafica/modificaClienteFacade.class.php?modo=start&idcliente=";
	const MODIFICA_CLIENTE_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
	const CANCELLA_CLIENTE_HREF = "<a class='tooltip' onclick='cancellaCliente(";
	const CANCELLA_CLIENTE_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

	const MODIFICA_FORNITORE_HREF = "<a class='tooltip' href='../anagrafica/modificaFornitoreFacade.class.php?modo=start&idfornitore=";
	const MODIFICA_FORNITORE_ICON = "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
	const CANCELLA_FORNITORE_HREF = "<a class='tooltip' onclick='cancellaFornitore(";
	const CANCELLA_FORNITORE_ICON = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

	// Actions

	const AZIONE_RICERCA_CLIENTE   = "../anagrafica/ricercaClienteFacade.class.php?modo=go";
	const AZIONE_RICERCA_FORNITORE = "../anagrafica/ricercaFornitoreFacade.class.php?modo=go";

	// Errori e messaggi

	const ERRORE_CODICE_FORNITORE		= "<br>&ndash; Manca il codice del fornitore";
	const ERRORE_DESCRIZIONE_FORNITORE	= "<br>&ndash; Manca la descrizione del fornitore";
	const ERRORE_CATEGORIA_CLIENTE		= "<br>&ndash; Manca la categoria del cliente";
	const ERRORE_CODICE_CLIENTE			= "<br>&ndash; Manca il codice del cliente";
	const ERRORE_DESCRIZIONE_CLIENTE	= "<br>&ndash; Manca la descrizione del cliente";
	const ERRORE_PIVA_CLIENTE			= "<br>&ndash; P.iva cliente gi&agrave; esistente";
	const ERRORE_CFISC_CLIENTE			= "<br>&ndash; C.fisc cliente gi&agrave; esistente";
	
	// Oggetti

	const RICERCA_CLIENTE_TEMPLATE   = "Obj_ricercaclientetemplate";
	const RICERCA_FORNITORE_TEMPLATE = "Obj_ricercafornitoretemplate";
	const CREA_FORNITORE_TEMPLATE    = "Obj_creafornitoretemplate";
	const CREA_CLIENTE_TEMPLATE		 = "Obj_creaclientetemplate";

	// Metodi

	public function getInstance();
	public function inizializzaPagina();
	public function controlliLogici();
	public function displayPagina();
}

?>
