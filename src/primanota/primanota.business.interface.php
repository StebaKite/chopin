<?php

require_once 'nexus6.main.interface.php';

interface PrimanotaBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const REGISTRAZIONE = "Obj_registrazione";
	const CAUSALE		= "Obj_causale";
	const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";

	const RICERCA_REGISTRAZIONE = "Obj_ricercaregistraione";
	const CREA_REGISTRAZIONE = "Obj_crearegistrazione";
	const AGGIUNGI_DETTAGLIO_REGISTRAZIONE = "Obj_aggiungidettaglioregistrazione";
	const CANCELLA_DETTAGLIO_REGISTRAZIONE = "Obj_cancelladettaglioregistrazione";
	const LOAD_CONTI_CAUSALE = "Obj_loadconticausale";
	const CERCA_FATTURA_FORNITORE = "Obj_cercafatturafornitore";
	const CERCA_FATTURA_CLIENTE = "Obj_cercafatturacliente";
	const VERIFICA_DETTAGLI_REGISTRAZIONE = "Obj_verificadettagliregistrazione";
	const CONTROLLA_DATA_REGISTRAZIONE = "Obj_controlladataregistrazione";
	const AGGIUNGI_SCADENZA_FORNITORE = "Obj_aggiungiscadenzafornitore";

	// Actions

	const AZIONE_RICERCA_REGISTRAZIONE = "../primanota/ricercaRegistrazioneFacade.class.php?modo=go";

	// Errori e messaggi

	const MSG_DA_CREAZIONE = "messaggioCreazione";
	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const MSG_DA_MODIFICA = "messaggioModifica";

	const CREA_REGISTRAZIONE_OK = "Registrazione salvata con successo";
	const ERRORE_CREAZIONE_REGISTRAZIONE = "ERRORE : registrazione non creata";

	const ERRORE_LETTURA = "Errore fatale durante la lettura delle registrazioni" ;

	// Metodi

	public function getInstance();
	public function start();
	public function go();
}

?>
