<?php

require_once 'nexus6.main.interface.php';

interface AnagraficaBusinessInterface extends MainNexus6Interface {

    // Nomi

    const CLIENTI = "clientiTrovati";
    const FORNITORI = "fornitoriTrovati";
    const MERCATI = "mercatiTrovati";
    const QTA_CLIENTI = "numClientiTrovati";
    const QTA_FORNITORI = "numFornitoriTrovati";
    const CATEGORIE_CLIENTE = "elenco_categorie_cliente";
    
    // Oggetti

    const RICERCA_CLIENTE = "Obj_ricercacliente";
    const RICERCA_FORNITORE = "Obj_ricercafornitore";
    const CREA_FORNITORE = "Obj_creafornitore";
    const CREA_CLIENTE = "Obj_creacliente";
    const CREA_MERCATO = "Obj_creamercato";
    const CANCELLA_CLIENTE = "Obj_cancellacliente";
    const CANCELLA_FORNITORE = "Obj_cancellafornitore";
    const CANCELLA_MERCATO = "Obj_cancellamercato";
    const CERCA_CFISC_CLIENTE = "Obj_cercacfisccliente";
    const CERCA_PIVA_CLIENTE = "Obj_cercapivacliente";
    const MODIFICA_CLIENTE = "Obj_modificacliente";
    const MODIFICA_FORNITORE = "Obj_modificafornitore";
    const MODIFICA_MERCATO = "Obj_modificamercato";
    const RICERCA_MERCATO = "Obj_ricercamercato";
    
    // Files XML

    const XML_FORNITORE = "/anagrafica/xml/fornitore.xml";
    const XML_CLIENTE = "/anagrafica/xml/cliente.xml";
    const XML_MERCATO = "/anagrafica/xml/mercato.xml";
    
    // Actions

    const AZIONE_RICERCA_CLIENTE = "../anagrafica/ricercaClienteFacade.class.php?modo=go";
    const AZIONE_RICERCA_FORNITORE = "../anagrafica/ricercaFornitoreFacade.class.php?modo=go";
    const AZIONE_CREA_FORNITORE = "../anagrafica/creaFornitoreFacade.class.php?modo=go";
    const AZIONE_CREA_CLIENTE = "../anagrafica/creaClienteFacade.class.php?modo=go";
    const AZIONE_MODIFICA_CLIENTE = "../anagrafica/modificaClienteFacade.class.php?modo=go";
    const AZIONE_MODIFICA_FORNITORE = "../anagrafica/modificaFornitoreFacade.class.php?modo=go";
    const AZIONE_RICERCA_MERCATO = "../anagrafica/ricercaMercatoFacade.class.php?modo=go";
    
    // Errori e messaggi

    const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
    const MSG_DA_CREAZIONE = "messaggioCreazione";
    const MSG_DA_MODIFICA = "messaggioModifica";
    const ERRORE_CREA_FORNITORE = "Fornitore già esistente, inserimento fallito";
    const ERRORE_CREA_CLIENTE = "Cliente già esistente, inserimento fallito";
    const CREA_FORNITORE_OK = "Fornitore salvato con successo";
    const CREA_CLIENTE_OK = "Cliente salvato con successo";
    const MODIFICA_FORNITORE_OK = "Fornitore salvato con successo";
    const MODIFICA_CLIENTE_OK = "Cliente salvato con successo";
    const CREA_MERCATO_OK = "Nuovo mercato creato con successo";
    const MODIFICA_MERCATO_OK = "Mercato modificato con successo";
    const CANCELLA_MERCATO_OK = "Mercato cancellato con successo";
    const CANCELLA_CLIENTE_OK = "Cliente cancellato";
    const ERRORE_CANCELLA_CLIENTE = "Errore, cliente non cancellato";

    // Metodi

    public static function getInstance();

    public function start();

    public function go();
}