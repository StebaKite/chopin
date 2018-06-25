<?php

require_once 'nexus6.main.interface.php';

/**
 *
 * @author stefano
 */
interface FattureBusinessInterface extends MainNexus6Interface {

    const CREA_FATTURA_AZIENDA_CONSORTILE = "Obj_creafatturaaziendaconsortile";
    const CREA_FATTURA_AZIENDA_CONSORTILE_TEMPLATE = "Obj_creafatturaaziendaconsortiletemplate";
    const PAGINA_CREA_FATTURA_AZIENDA_CONSORTILE = "/fatture/creaFatturaAziendaConsortile.form.html";
    const FATTURA_AZIENDA_CONSORTILE = "Obj_fatturaaziendaconsortile";
    const FATTURA = "Obj_fattura";

    public function getInstance();

    public function start();

    public function go();
}
