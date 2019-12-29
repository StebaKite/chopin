<?php

require_once 'nexus6.main.interface.php';

/**
 *
 * @author stefano
 */
interface FattureBusinessInterface extends MainNexus6Interface {

    const CREA_FATTURA_AZIENDA_CONSORTILE = "Obj_creafatturaaziendaconsortile";
    const CREA_FATTURA_CLIENTE = "Obj_creafatturacliente";
    const CREA_FATTURA_CLIENTE_XML = "Obj_creafatturacliente_xml";
    const CREA_FATTURA_ENTE_PUBBLICO = "Obj_creafatturaentepubblico";
    const CREA_FATTURA_AZIENDA_CONSORTILE_TEMPLATE = "Obj_creafatturaaziendaconsortiletemplate";
    const CREA_FATTURA_CLIENTE_TEMPLATE = "Obj_creafatturaclientetemplate";
    const CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE = "Obj_creafatturaentepubblicotemplate";
    const PRELEVA_PROGRESSIVO_FATTURA = "Obj_prelevaprogressivofattura";
    const PRELEVA_TIPO_ADDEBITO_CLIENTE = "Obj_prelevatipoaddebitocliente";
    const PAGINA_CREA_FATTURA_AZIENDA_CONSORTILE = "/fatture/creaFatturaAziendaConsortile.form.html";
    const PAGINA_CREA_FATTURA_CLIENTE = "/fatture/creaFatturaCliente.form.html";
    const PAGINA_CREA_FATTURA_ENTE_PUBBLICO = "/fatture/creaFatturaEntePubblico.form.html";
    const FATTURA_AZIENDA_CONSORTILE = "Obj_fatturaaziendaconsortile";
    const FATTURA_CLIENTE = "Obj_fatturacliente";
    const FATTURA_ENTE_PUBBLICO = "Obj_fatturaentepubblico";
//    const FATTURA = "Obj_fattura";
    const AGGIUNGI_DETTAGLIO_FATTURA = "Obj_aggiungidetaggliofattura";
    const CANCELLA_DETTAGLIO_FATTURA = "Obj_cancelladetaggliofattura";

    public static function getInstance();

    public function start();

    public function go();
}
