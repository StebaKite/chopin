<?php

require_once 'nexus6.main.interface.php';

interface PrimanotaBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const REGISTRAZIONE = "Obj_registrazione";
	const CAUSALE		= "Obj_causale";
	const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";

	const RICERCA_REGISTRAZIONE = "Obj_ricercaregistraione";
	const CREA_REGISTRAZIONE = "Obj_crearegistrazione";
	const CREA_INCASSO = "Obj_creaincasso";
	const CREA_PAGAMENTO = "Obj_creapagamento";
	const AGGIORNA_IMPORTO_DETTAGLIO_REGISTRAZIONE = "Obj_aggiornaimportodettaglioregistrazione";
	const AGGIORNA_SEGNO_DETTAGLIO_REGISTRAZIONE = "Obj_aggiornasegnodettaglioregistrazione";
	const AGGIUNGI_DETTAGLIO_REGISTRAZIONE = "Obj_aggiungidettaglioregistrazione";
	const AGGIUNGI_NUOVO_DETTAGLIO_CONTO_FORNITORE = "Obj_aggiunginuovodettagliocontofornitore";
	const AGGIUNGI_NUOVO_DETTAGLIO_CONTO_CLIENTE = "Obj_aggiunginuovodettagliocontocliente";
	const CANCELLA_DETTAGLIO_REGISTRAZIONE = "Obj_cancelladettaglioregistrazione";
	const LOAD_CONTI_CAUSALE = "Obj_loadconticausale";
	const CERCA_FATTURA_FORNITORE = "Obj_cercafatturafornitore";
	const CERCA_FATTURA_CLIENTE = "Obj_cercafatturacliente";
	const VERIFICA_DETTAGLI_REGISTRAZIONE = "Obj_verificadettagliregistrazione";
	const CONTROLLA_DATA_REGISTRAZIONE = "Obj_controlladataregistrazione";
	const AGGIUNGI_SCADENZA_FORNITORE = "Obj_aggiungiscadenzafornitore";
	const CALCOLA_DATA_SCADENZA_FORNITORE = "Obj_calcoladatascadenzafornitore";
	const AGGIUNGI_SCADENZA_CLIENTE = "Obj_aggiungiscadenzacliente";
	const CANCELLA_SCADENZA_FORNITORE = "Obj_cancellascadenzafornitore";
	const CANCELLA_SCADENZA_CLIENTE = "Obj_cancellascadenzacliente";
	const AGGIORNA_IMPORTO_SCADENZA_FORNITORE = "Obj_aggiornaimportoscadenzafornitore";
	const AGGIORNA_IMPORTO_SCADENZA_CLIENTE = "Obj_aggiornaimportoscadenzacliente";
	const RICERCA_SCADENZE_CLIENTE_APERTE = "Obj_ricercascadenzeclienteaperte";
	const RICERCA_SCADENZE_FORNITORE_APERTE = "Obj_ricercascadenzefornitoreaperte";
	const ANNULLA_NUOVA_REGISTRAZIONE = "Obj_annullanuovaregistrazione";
	const ANNULLA_NUOVO_INCASSO = "Obj_annullanuovoincasso";
	const ANNULLA_NUOVO_PAGAMENTO = "Obj_annullanuovopagamento";
	const LEGGI_MERCATI_NEGOZIO = "Obj_leggimercatinegozio";
	const AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO = "Obj_aggiungidettagliocorrispettivomercato";
	const CREA_CORRISPETTIVO_MERCATO = "Obj_creacorrispettivomercato";
	const AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO = "Obj_aggiungidettagliocorrispettivonegozio";
	const CREA_CORRISPETTIVO_NEGOZIO = "Obj_creacorrispettivonegozio";

	// Actions

	const AZIONE_RICERCA_REGISTRAZIONE = "../primanota/ricercaRegistrazioneFacade.class.php?modo=go";

	// Errori e messaggi

	const MSG_DA_CREAZIONE = "messaggioCreazione";
	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const MSG_DA_MODIFICA = "messaggioModifica";

	const CREA_REGISTRAZIONE_OK = "Registrazione salvata con successo";
	const CREA_INCASSO_OK = "Incasso salvato con successo";
	const CREA_PAGAMENTO_OK = "Pagamento salvato con successo";
	const ERRORE_CREAZIONE_REGISTRAZIONE = "ERRORE : registrazione non creata";

	const ERRORE_LETTURA = "Errore fatale durante la lettura delle registrazioni" ;

	// Metodi

	public function getInstance();
	public function start();
	public function go();
}

?>
