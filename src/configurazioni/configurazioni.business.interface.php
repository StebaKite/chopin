<?php

require_once 'nexus6.main.interface.php';

interface ConfigurazioniBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const CONTO = "Obj_conto";
	const SOTTOCONTO = "Obj_sottoconto";
	const CAUSALE = "Obj_causale";
	const CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";
	const PROGRESIVO_FATTURA = "Obj_progressivofattura";

	const RICERCA_CONTO = "Obj_ricercaconto";
	const MODIFICA_CONTO = "Obj_modificaconto";
	const VISUALIZZA_CONTO = "Obj_visualizzaconto";
	const CANCELLA_CONTO = "Obj_cancellaconto";
	const CREA_CONTO = "Obj_creaconto";
	const CONTROLLA_CONTO = "Obj_controllaconto";
	const CANCELLA_SOTTOCONTO = "Obj_cancellasottoconto";
	const AGGIUNGI_SOTTOCONTO = "Obj_aggiungisottoconto";
	const TOGLI_SOTTOCONTO = "Obj_toglisottoconto";
	const ESTRAI_MOVIMENTI_SOTTOCONTO = "Obj_estraimovimentisottoconto";
	const ESPORTA_MOVIMENTI_SOTTOCONTO = "Obj_esportamovimentisottoconto";
	const RICERCA_CAUSALE = "Obj_ricercacausale";
	const CREA_CAUSALE = "Obj_creacausale";
	const CANCELLA_CAUSALE = "Obj_cancellacausale";
	const MODIFICA_CAUSALE = "Obj_modificacausale";
	const RICERCA_PROGRESSIVO_FATTURA = "Obj_ricercaprogressivofattura";
	const MODIFICA_PROGRESSIVO_FATTURA = "Obj_modificaprogressivofattura";
	const CONFIGURA_CAUSALE = "Obj_configuracausale";
	const INCLUDI_CONTO_CAUSALE = "Obj_includicontocausale";
	const ESCLUDI_CONTO_CAUSALE = "Obj_escludicontocausale";
	const INSERISCI_SOTTOCONTO = "Obj_inseriscisottoconto";
	const MODIFICA_GRUPPO_SOTTOCONTO = "Obj_modificagrupposottoconto";
    
    // Files XML
    
    const XML_CONTO = "/configurazioni/xml/conto.xml";
    const XML_SOTTOCONTO = "/configurazioni/xml/sottoconto.xml";
    const XML_CAUSALE = "/configurazioni/xml/causale.xml";
    const XML_PROGRESSIVO = "/configurazioni/xml/progressivo.xml"; 
    
	// Actions

    const AZIONE_RICERCA_CONTO = "../configurazioni/ricercaContoFacade.class.php?modo=go";
    const AZIONE_RICERCA_CAUSALE = "../configurazioni/ricercaCausaleFacade.class.php?modo=go";
    const AZIONE_RICERCA_PROGRESSIVO_FATTURA = "../configurazioni/ricercaProgressivoFatturaFacade.class.php?modo=go";

	// Metodi

 	public function getInstance();
 	public function start();
 	public function go();
}

?>
