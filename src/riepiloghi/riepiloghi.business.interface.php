<?php

require_once 'nexus6.main.interface.php';

/**
 *
 * @author stefano
 */
interface RiepiloghiBusinessInterface extends MainNexus6Interface {

    const GENERA_BILANCIO_PERIODICO = "Obj_generabilancioperiodico";
    const GENERA_BILANCIO_ESERCIZIO = "Obj_generabilancioesercizio";
    const ESTRAI_PDF_BILANCIO = "Obj_estraipdfbilancio";
    const ESTRAI_PDF_RIEPILOGO_NEGOZIO = "Obj_estraipdfriepilogonegozio";
    const ESTRAI_PDF_ANDAMENTO_NEGOZIO = "Obj_estraipdfandamentonegozio";
    const RIEPILOGO_NEGOZI = "Obj_riepilogonegozi";
    const ANDAMENTO_NEGOZI = "Obj_andamentonegozi";
    const ANDAMENTO_MERCATI = "Obj_andamentomercati";
    //
    const AZIONE_BILANCIO_PERIODICO = "../riepiloghi/generaBilancioPeriodicoFacade.class.php?modo=go";
    const AZIONE_BILANCIO_ESERCIZIO = "../riepiloghi/generaBilancioEsercizioFacade.class.php?modo=go";
    const AZIONE_RIEPILOGO_NEGOZI = "../riepiloghi/riepilogoNegoziFacade.class.php?modo=go";
    const AZIONE_ANDAMENTO_NEGOZI = "../riepiloghi/andamentoNegoziFacade.class.php?modo=go";
    const AZIONE_ANDAMENTO_MERCATI = "../riepiloghi/andamentoMercatiFacade.class.php?modo=go";
    //
    const ESERCIZIO = "Esercizio";
    const PERIODICO = "Periodico";

    public function getInstance();

    public function start();

    public function go();
}
