<?php

require_once 'nexus6.main.interface.php';

interface PrimanotaBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const REGISTRAZIONE      = "Obj_registrazione";
	const CAUSALE		     = "Obj_causale";
	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
	const SCADENZA_CLIENTE   = "Obj_scadenzacliente";

	const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";
	const RICERCA_REGISTRAZIONE = "Obj_ricercaregistraione";
	const CREA_REGISTRAZIONE = "Obj_crearegistrazione";
	const MODIFICA_REGISTRAZIONE = "Obj_modificaregistrazione";
	const MODIFICA_PAGAMENTO = "Obj_modificapagamento";
	
	const CREA_INCASSO = "Obj_creaincasso";
	const MODIFICA_INCASSO = "Obj_modificaincasso";
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
	const CALCOLA_DATA_SCADENZA_CLIENTE = "Obj_calcoladatascadenzacliente";
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
	const ANNULLA_NUOVO_CORRISPETTIVO_MERCATO = "Obj_annullanuovocorrispettivomercato";
	const ANNULLA_NUOVO_CORRISPETTIVO_NEGOZIO = "Obj_annullanuovocorrispettivonegozio";
	const ANNULLA_MODIFICA_REGISTRAZIONE = "Obj_annullamodificaregistrazione";
	const LEGGI_MERCATI_NEGOZIO = "Obj_leggimercatinegozio";
	const AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO = "Obj_aggiungidettagliocorrispettivomercato";
	const CREA_CORRISPETTIVO_MERCATO = "Obj_creacorrispettivomercato";
	const AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO = "Obj_aggiungidettagliocorrispettivonegozio";
	const CREA_CORRISPETTIVO_NEGOZIO = "Obj_creacorrispettivonegozio";
	const AGGIUNGI_FATTURA_PAGATA = "Obj_aggiungifatturapagata";
	const RIMUOVI_FATTURA_PAGATA = "Obj_rimuovifatturapagata";
	const AGGIUNGI_FATTURA_INCASSATA = "Obj_aggiungifatturaincassata";
	const RIMUOVI_FATTURA_INCASSATA = "Obj_rimuovifatturaincassata";
	
	// Actions

	const AZIONE_RICERCA_REGISTRAZIONE = "../primanota/ricercaRegistrazioneFacade.class.php?modo=go";

	// Errori e messaggi

	const MSG_DA_CREAZIONE = "messaggioCreazione";
	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const MSG_DA_MODIFICA = "messaggioModifica";

	const CREA_REGISTRAZIONE_OK = "Registrazione salvata con successo";
	const CREA_INCASSO_OK = "Incasso salvato con successo";
	const CREA_PAGAMENTO_OK = "Pagamento salvato con successo";
	
	const MODIFICA_PAGAMENTO_OK = "Pagamento modificato con successo";
	const MODIFICA_INCASSO_OK = "Incasso modificato con successo";
	
	const ERRORE_CREAZIONE_REGISTRAZIONE = "ERRORE : registrazione non creata";
	const ERRORE_MODIFICA_REGISTRAZIONE = "ERRORE : registrazione non modificata";
	
	const ERRORE_LETTURA = "Errore fatale durante la lettura delle registrazioni" ;

	// Metodi

	public function getInstance();
	public function start();
	public function go();
}

?>
