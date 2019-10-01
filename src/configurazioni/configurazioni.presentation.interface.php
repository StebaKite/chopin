<?php

require_once 'nexus6.main.interface.php';

interface ConfigurazioniPresentationInterface extends MainNexus6Interface {

    // Nomi

    const CONTI = "contiTrovati";
    const QTA_CONTI = "numContiTrovati";
    const TITOLO = "titoloPagina";
    const NUM_REG_CONTO = "tot_registrazioni_conto";
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

    const VISUALIZZA_CONTO_HREF = "<a onclick='visualizzaConto(";
    const MODIFICA_CONTO_HREF = "<a onclick='modificaConto(";
    const CANCELLA_CONTO_HREF = "<a onclick='cancellaConto(";
    const GENERA_MASTRINO_HREF = "<a onclick='generaMastrino(";
    const ESTRAI_PDF = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
    const MODIFICA_CAUSALE_HREF = "<a onclick='modificaCausale(";
    const CONFIGURA_CAUSALE_HREF = "<a onclick='configuraCausale(";
    const CANCELLA_CAUSALE_HREF = "<a onclick='cancellaCausale(";
    const MODIFICA_PROGRESSIVO_HREF = "<a onclick='modificaProgressivoFattura(";
    // Errori e messaggi
 	
    // Oggetti

    const RICERCA_CONTO_TEMPLATE = "Obj_ricercacontotemplate";
    const CREA_CONTO_TEMPLATE = "Obj_creacontotemplate";
    const MODIFICA_CONTO_TEMPLATE = "Obj_modificacontotemplate";
    const GENERA_MASTRINO_TEMPLATE = "Obj_generamastrinotemplate";
    const RICERCA_CAUSALI_TEMPLATE = "Obj_ricercacausalitemplate";
    const CREA_CAUSALE_TEMPLATE = "Obj_creacausaletemplate";
    const RICERCA_PROGRESSIVO_FATTURA_TEMPLATE = "Obj_ricercaprogressivofatturatemplate";
    const AGGIORNA_PROGRESSIVO_FATTURA_TEMPLATE = "Obj_aggiornaprogressivofatturatemplate";
    const CONFIGURA_CAUSALE_TEMPLATE = "Obj_configuracausaletemplate";
    const MODIFICA_CAUSALE_TEMPLATE = "Obj_modificacausaletemplate";

    // Metodi

    public static function getInstance();

    public function inizializzaPagina();

    public function controlliLogici();

    public function displayPagina();
}