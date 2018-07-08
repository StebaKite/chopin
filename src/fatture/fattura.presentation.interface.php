<?php

require_once 'nexus6.main.interface.php';

interface FatturaPresentationInterface extends MainNexus6Interface {

    // Nomi
    // Pagine
    // Files XML
    // Bottoni

    const CANCELLA_DETTAGLIO_FATTURA_HREF = "<a onclick='cancellaDettaglioFattura(";

    // Errori e messaggi
    // Oggetti
    // Metodi

    public function getInstance();
}

?>