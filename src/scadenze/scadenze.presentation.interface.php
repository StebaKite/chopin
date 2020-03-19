<?php

require_once 'nexus6.main.interface.php';

interface ScadenzePresentationInterface extends MainNexus6Interface {

    /**
     *  Nomi
     */
    const SCADENZE_FORNITORE = "scadenzeFornitoreTrovate";

    /**
     *  Pagine
     */
    const PAGINA_RICERCA_SCADENZE_FORNITORE = "/scadenze/ricercaScadenzeFornitore.form.html";
    const PAGINA_RICERCA_SCADENZE_CLIENTE = "/scadenze/ricercaScadenzeCliente.form.html";

    /**
     *  Bottoni
     */
    const MODIFICA_REGISTRAZIONE_HREF = "<a onclick='modificaRegistrazione(";
    const VISUALIZZA_REGISTRAZIONE_HREF = "<a onclick='visualizzaRegistrazione(";
    const MODIFICA_PAGAMENTO_HREF = "<a onclick='modificaPagamento(";
    const VISUALIZZA_PAGAMENTO_HREF = "<a onclick='visualizzaPagamento(";
    const VISUALIZZA_INCASSO_HREF = "<a onclick='visualizzaIncasso(";
    const VISUALIZZA_SCADENZA_HREF = "<a onclick='visualizzaScadenzaFornitore(";
    const MODIFICA_SCADENZA_HREF = "<a onclick='modificaScadenzaFornitore(";
    const CANCELLA_SCADENZA_HREF = "<a onclick='cancellaScadenzaFornitore(";
    const VISUALIZZA_SCADENZA_CLIENTE_HREF = "<a onclick='visualizzaScadenzaCliente(";
    const MODIFICA_SCADENZA_CLIENTE_HREF = "<a onclick='modificaScadenzaCliente(";
    const MODIFICA_INCASSO_HREF = "<a onclick='modificaIncasso(";
    const CANCELLA_INCASSO_HREF = "<a onclick='cancellaIncasso(";
    const CANCELLA_PAGAMENTO_HREF = "<a onclick='cancellaPagamento(";

    /**
     *  Errori e messaggi
     */
    const DATA_KO = "class='bg-danger'";
    const DATA_OK = "";
    const DATA_CHIUSA = "class='bg-info'";
    const ERRORE_DATA_INIZIO_RICERCA = "<br>&ndash; Manca la data di inizio ricerca";
    const ERRORE_DATA_FINE_RICERCA = "<br>&ndash; Manca la data di fine ricerca";
    // Oggetti

    const RICERCA_SCADENZE_FORNITORE_TEMPLATE = "Obj_ricercascadenzefornitoretemplate";
    const RICERCA_SCADENZE_CLIENTE_TEMPLATE = "Obj_ricercascadenzeclientetemplate";

    // Metodi

    public static function getInstance();
// 	public function inizializzaPagina();
// 	public function controlliLogici();
// 	public function displayPagina();
}

?>