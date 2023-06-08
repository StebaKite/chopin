<?php

interface MainNexus6Interface {

    /**
     *  Negozi
     */
    const NEGOZIO_VILLA = "Villa D'Adda";
    const NEGOZIO_BREMBATE = "Brembate";
    const NEGOZIO_TREZZO = "Trezzo";

    /**
     *  Costanti
     */
    const TESTATA = "testataPagina";
    const PIEDE = "piedePagina";
    const ERRORE = "messaggioErrore";
    const INFO = "messaggioInfo";
    const AMBIENTE = "ambiente";
    const USERS = "users";
    const MESSAGGIO = "messaggio";
    const MSG = "divmsg";
    const AZIONE = "azione";
    const TIP_CONFERMA = "confermaTip";
    const TITOLO_PAGINA = "titoloPagina";
    const LOGO = "logo";
    const CREATORE = "creator";
    const NEXUS6 = "Nexus6";
    const PDF_TITLE = "title";
    const PDF_SOTTOTITOLO = "title1";
    const UTILE = "Utile";
    const PAREGGIO = "Pareggio";
    const PERDITA = "Perdita";
    const CONTRIBUTO = "CONTRIBUTO";
    const VENDITA = "VENDITA";

    /**
     *  Messaggi
     */
    const EMPTYSTRING = "";
    const ZERO_VALUE = 0;
    const CAMPO_VUOTO = "&ndash;&ndash;&ndash;";
    const DATA_ALTA = "31/12/9999";
    const SELECT_THIS_ITEM = "selected";
    const CHECK_THIS_ITEM = "checked";
    const ACTIVE_THIS_ITEM = "active";
    const ERBA = "ERB";

    /**
     *  Costanti comuni
     */
    const EURO = 'EURO';
    const SCADENZA_APERTA = "00";
    const SCADENZA_CHIUSA = "10";
    const SCADENZA_RIMANDATA = '02';
    const SCADENZA_SOSPESA = "  ";
    const SCADENZA_DA_PAGARE = "Da Pagare";
    const SCADENZA_DA_INCASSARE = "Da Incassare";
    const SCADENZA_PAGATA = "Pagato";
    const SCADENZA_INCASSATA = "Incassato";
    const SCADENZA_POSTICIPATA = "Posticipato";
    const CONTO_IN_DARE = "Dare";
    const CONTO_IN_AVERE = "Avere";
    const FUNCTION_REFERER = "FunctionReferer";
    const CONTO_ECONOMICO = "Conto Economico";
    const STATO_PATRIMONIALE = "Stato Patrimoniale";
    const TUTTI_CONTI = "N";
    const SALDI_INCLUSI = "S";
    const SALDI_ESCLUSI = "N";

    /**
     *  Bottoni
     */
    const VISUALIZZA_ICON = ")'><span class='glyphicon glyphicon-eye-open'></span></a>";
    const MODIFICA_ICON = ")'><span class='glyphicon glyphicon-edit'></span></a>";
    const CANCELLA_ICON = ")'><span class='glyphicon glyphicon-trash'></span></a>";
    const OK_ICON = "<span class='glyphicon glyphicon-ok'></span>";
    const LISTA_ICON = ")'><span class='glyphicon glyphicon-list-alt'></span></a>";
    const CONFIGURA_ICON = ")'><span class='glyphicon glyphicon-wrench'></span></a>";
    const INCLUDI_ICON = ")'><span class='glyphicon glyphicon-triangle-left'></span></a>";
    const ESCLUDI_ICON = ")'><span class='glyphicon glyphicon-triangle-right'></span></a>";

    /**
     *  Messaggi d'errore comuni
     */
    const ERRORE_LETTURA = "Errore fatale durante la lettura dei dati!!";

    /**
     *  Parti HTML condivise da più pagine
     */
    const DIALOGO_VISUALIZZA_REGISTRAZIONE = "/condivisi/visualizzaRegistrazione.dialog.html";
    const DIALOGO_NUOVA_REGISTRAZIONE = "/condivisi/nuovaRegistrazione.dialog.html";
    const DIALOGO_CANCELLA_REGISTRAZIONE = "/condivisi/cancellaRegistrazione.dialog.html";
    const DIALOGO_NUOVO_INCASSO = "/condivisi/nuovoIncasso.dialog.html";
    const DIALOGO_NUOVO_PAGAMENTO = "/condivisi/nuovoPagamento.dialog.html";
    const DIALOGO_NUOVO_CORRISPETTIVO_MERCATO = "/condivisi/nuovoCorrispettivoMercato.dialog.html";
    const DIALOGO_NUOVO_CORRISPETTIVO_NEGOZIO = "/condivisi/nuovoCorrispettivoNegozio.dialog.html";
    const DIALOGO_VISUALIZZA_PAGAMENTO = "/condivisi/visualizzaPagamento.dialog.html";
    const DIALOGO_CANCELLA_PAGAMENTO = "/condivisi/cancellaPagamento.dialog.html";
    const DIALOGO_VISUALIZZA_INCASSO = "/condivisi/visualizzaIncasso.dialog.html";
    const DIALOGO_VISUALIZZA_CORRISPETTIVO_MERCATO = "/condivisi/visualizzaCorrispettivoMercato.dialog.html";
    const DIALOGO_VISUALIZZA_CORRISPETTIVO_NEGOZIO = "/condivisi/visualizzaCorrispettivoNegozio.dialog.html";
    const DIALOGO_MODIFICA_INCASSO = "/condivisi/modificaIncasso.dialog.html";
    const DIALOGO_MODIFICA_REGISTRAZIONE = "/condivisi/modificaRegistrazione.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_NUOVA_REGISTRAZIONE = "/condivisi/nuovoDettaglioNuovaRegistrazione.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_MODIFICA_REGISTRAZIONE = "/condivisi/nuovoDettaglioModificaRegistrazione.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_NUOVO_INCASSO = "/condivisi/nuovoDettaglioNuovoIncasso.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_NUOVO_PAGAMENTO = "/condivisi/nuovoDettaglioNuovoPagamento.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_MODIFICA_INCASSO = "/condivisi/nuovoDettaglioModificaIncasso.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_NUOVO_CORRISPETTIVO_MERCATO = "/condivisi/nuovoDettaglioNuovoCorrispettivoMercato.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_MODIFICA_CORRISPETTIVO_MERCATO = "/condivisi/nuovoDettaglioModificaCorrispettivoMercato.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_MODIFICA_CORRISPETTIVO_NEGOZIO = "/condivisi/nuovoDettaglioModificaCorrispettivoNegozio.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_NUOVO_CORRISPETTIVO_NEGOZIO = "/condivisi/nuovoDettaglioNuovoCorrispettivoNegozio.dialog.html";
    const DIALOGO_NUOVA_SCADENZA_NUOVA_REGISTRAZIONE = "/condivisi/nuovaDataScadenzaNuovaRegistrazione.dialog.html";
    const DIALOGO_NUOVA_SCADENZA_MODIFICA_REGISTRAZIONE = "/condivisi/nuovaDataScadenzaModificaRegistrazione.dialog.html";
    const DIALOGO_MODIFICA_PAGAMENTO = "/condivisi/modificaPagamento.dialog.html";
    const DIALOGO_MODIFICA_CORRISPETTIVO_MERCATO = "/condivisi/modificaCorrispettivoMercato.dialog.html";
    const DIALOGO_MODIFICA_CORRISPETTIVO_NEGOZIO = "/condivisi/modificaCorrispettivoNegozio.dialog.html";
    const DIALOGO_NUOVO_DETTAGLIO_MODIFICA_PAGAMENTO = "/condivisi/nuovoDettaglioModificaPagamento.dialog.html";

    /**
     *  Oggetti condivisi da più classi
     */
    const FORNITORE = "Obj_fornitore";
    const CLIENTE = "Obj_cliente";
    const RICERCA_SCADENZE_FORNITORE = "Obj_ricercascadenzefornitore";
    const RICERCA_SCADENZE_CLIENTE = "Obj_ricercascadenzecliente";
    const PRIMANOTA_CONTROLLER = "Obj_primanotacontroller";
    const SCADENZE_CONTROLLER = "Obj_scadenzecontroller";
    const RIEPILOGO = "Obj_riepilogo";
    const RIPORTO_SALDO = "Obj_riportosaldo";
    const PIANIFICAZIONE_LAVORI_PRIMO_SEMESTRE = "Obj_pinificazionelavoriprimosemestre";
    const PIANIFICAZIONE_LAVORI_SECONDO_SEMESTRE = "Obj_pinificazionelavorisecondosemestre";
    const REGISTRAZIONE = "Obj_registrazione";
    const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";
    const CAUSALE = "Obj_causale";
    const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
    const SCADENZA_CLIENTE = "Obj_scadenzacliente";
    const MERCATO = "Obj_mercato";   
    const CONTROLLI_APERTURA = "Obj_controlliapertura";
    const CONTO = "Obj_conto";
    const SOTTOCONTO = "Obj_sottoconto";
    const CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";
    const PROGRESSIVO_FATTURA = "Obj_progressivofattura";
    const FATTURA = "Obj_fattura";
    const DETTAGLIO_FATTURA = "Obj_dettagliofattura";
    const BILANCIO = "Obj_bilancio";
    const SALDO = "Obj_saldo";
    const CORRISPETTIVO = "Obj_corrispettivo";    
    const CATEGORIA_CLIENTE_OBJ = "Obj_categoriacliente";
    const PRESENZA_ASSISTITO = "Obj_presenzaassistito";
    const ASSISTITO = "Obj_assistito";

}

?>