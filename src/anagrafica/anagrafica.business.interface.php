<?php

require_once 'nexus6.main.interface.php';

interface AnagraficaBusinessInterface extends MainNexus6Interface {
	
	// Nomi
	
	const CLIENTI = "clientiTrovati";
	const FORNITORI = "fornitoriTrovati";
	const QTA_CLIENTI = "numClientiTrovati";
	const QTA_FORNITORI = "numFornitoriTrovati";
	const CATEGORIE_CLIENTE = "elenco_categorie_cliente"; 
		
	// Oggetti

	const RICERCA_CLIENTE = "Obj_ricercacliente";
	const RICERCA_FORNITORE = "Obj_ricercafornitore";
	const CREA_FORNITORE = "Obj_creafornitore";
	const FORNITORE = "Obj_fornitore";
	
	// Actions
	
	const AZIONE_RICERCA_CLIENTE   = "../anagrafica/ricercaClienteFacade.class.php?modo=go";
	const AZIONE_RICERCA_FORNITORE = "../anagrafica/ricercaFornitoreFacade.class.php?modo=go";
	const AZIONE_CREA_FORNITORE	   = "../anagrafica/creaFornitoreFacade.class.php?modo=go";
	
	// Queries
	
	const QUERY_RICERCA_CLIENTE   = "/anagrafica/ricercaCliente.sql";
	const QUERY_RICERCA_FORNITORE = "/anagrafica/ricercaFornitore.sql";

	// Errori e messaggi
	
	const ERRORE_LETTURA = "Errore fatale durante la lettura dei dati";
	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const ERRORE_CREA_FORNITORE = "Fornitore già esistente, inserimento fallito";
	const CREA_FORNITORE_OK = "Fornitore salvato con successo";
	
	// Metodi
	
 	public function getInstance();
 	public function start();
 	public function go();
}

?>