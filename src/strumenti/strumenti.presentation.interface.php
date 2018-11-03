<?php

require_once 'nexus6.main.interface.php';

/**
 *
 * @author BarbieriStefano
 */
interface StrumentiPresentationInterface extends MainNexus6Interface {

    const CAMBIA_CONTO_STEP1_TEMPLATE = "Obj_cambiacontostep1templatwe";
    const CAMBIA_CONTO_STEP2_TEMPLATE = "Obj_cambiacontostep2templatwe";
    const CAMBIA_CONTO_STEP3_TEMPLATE = "Obj_cambiacontostep3templatwe";
    
    /**
     *  Pagine
     */
    const PAGINA_CAMBIO_CONTO_STEP1 = "/strumenti/cambiaContoStep1.form.html";
    const PAGINA_CAMBIO_CONTO_STEP2 = "/strumenti/cambiaContoStep2.form.html";
    const PAGINA_CAMBIO_CONTO_STEP3 = "/strumenti/cambiaContoStep3.form.html";
    
    
    
    
    
    
}
