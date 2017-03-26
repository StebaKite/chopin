<?php

require_once 'nexus6.main.interface.php';

interface ConfigurazioniBusinessInterface extends MainNexus6Interface {

	// Nomi

	// Oggetti

 	const RICERCA_CONTO = "Obj_ricercaconto";
 	const MODIFICA_CONTO = "Obj_modificaconto";
 	const CANCELLA_CONTO = "Obj_cancellaconto";
 	const CREA_CONTO = "Obj_creaconto";
    const CONTO = "Obj_conto";
    const SOTTOCONTO = "Obj_sottoconto";
    const CANCELLA_SOTTOCONTO = "Obj_cancellasottoconto";
    const AGGIUNGI_SOTTOCONTO = "Obj_aggiungisottoconto";
    const TOGLI_SOTTOCONTO = "Obj_toglisottoconto";
    const ESTRAI_MASTRINO = "Obj_estraimastrino";
    const ESTRAI_PDF_MASTRINO = "Obj_estraipdfmastrino";

	// Actions

	const AZIONE_RICERCA_CONTO = "../configurazioni/ricercaContoFacade.class.php?modo=go";
	const AZIONE_MODIFICA_CONTO = "../configurazioni/modificaContoFacade.class.php?modo=go";
	const AZIONE_GENERA_MASTRINO = "../configurazioni/generaMastrinoContoFacade.class.php?modo=go";	
	const AZIONE_ESTRAI_PDF_MASTRINO = "../configurazioni/estraiPdfMastrinoContoFacade.class.php?modo=go";
	const AZIONE_CREA_CONTO = "../configurazioni/creaContoFacade.class.php?modo=go";
	
	// Errori e messaggi

 	const ERRORE_LETTURA = "Errore fatale durante la lettura dei dati";
 	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
 	const MSG_DA_GENERAZIONE_MASTRINO = "messaggioGeneraMastrino";
 	const MSG_DA_CREAZIONE_CONTO = "Conto salvato con successo";
 	const ERRORE_CREAZIONE_CONTO = "Attenzione: conto non inserito!";
 	const GENERA_MASTRINO_OK = "Mastrino del conto generato!";
 	const REGISTRAZIONI_NON_TROVATE = "Nessuna registrazione trovata!";
 	const CREA_CONTO_OK = "Conto salvato con successo";
 	const CANCELLA_CONTO_OK = "Conto cancellato";

	// Metodi

 	public function getInstance();
 	public function start();
 	public function go();
}

?>
