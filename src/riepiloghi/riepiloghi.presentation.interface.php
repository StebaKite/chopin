<?php

require_once 'nexus6.main.interface.php';

interface RiepiloghiPresentationInterface extends MainNexus6Interface {
    /**
     *  Nomi
     */

    /**
     *  Pagine
     */
    const PAGINA_GENERA_BILANCIO_ESERCIZIO = "/riepiloghi/generaBilancioEsercizio.form.html";
    const PAGINA_GENERA_BILANCIO_PERIODICO = "/riepiloghi/generaBilancioPeriodico.form.html";
    const PAGINA_RIEPILOGO_NEGOZI = "/riepiloghi/riepilogoNegozi.form.html";

    /**
     *  Files XML
     */
    /**
     *  Bottoni
     */
    /**
     *  Metodi
     */

    /**
     *  Oggetti
     */
    const GENERA_BILANCIO_ESERCIZIO_TEMPLATE = "Obj_generabilancioeserciziotemplate";
    const GENERA_BILANCIO_PERIODICO_TEMPLATE = "Obj_generabilancioperiodicotemplate";
    const RIEPILOGO_NEGOZI_TEMPLATE = "Obj_riepilogonegozitemplate";
    const ANDAMENTO_NEGOZI_TEMPLATE = "Obj_andamentonegozitemplate";
    const RIEPILOGO_MERCATI_TEMPLATE = "Obj_riepilogomercatitemplate";

    /**
     * Metodi
     */
    public static function getInstance();

    public function inizializzaPagina();

    public function controlliLogici();

    public function displayPagina();
}

?>