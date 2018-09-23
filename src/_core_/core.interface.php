<?php

require_once 'nexus6.main.interface.php';

interface CoreInterface extends MainNexus6Interface {

    // Oggetti

    const SOTTOCONTO = "Obj_sottoconto";
    const CONTO = "Obj_conto";
    const CATEGORIA_CLIENTE = "Obj_categoriacliente";
    const MERCATO = "Obj_mercato";
    const NEGOZIO = "Obj_negozio";
    const REGISTRAZIONE = "Obj_registrazione";
    const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";
    const CAUSALE = "Obj_causale";
    const CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";
    const PROGRESSIVO_FATTURA = "Obj_progressivofattura";
    const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
    const SCADENZA_CLIENTE = "Obj_scadenzacliente";
    const SALDO = "Obj_saldo";
    const LAVORO_PIANIFICATO = "Obj_lavoropianificato";
    const BILANCIO = "Obj_bilancio";
    const FATTURA = "Obj_fattura";
    const DETTAGLIO_FATTURA = "Obj_dettagliofattura";

}

?>