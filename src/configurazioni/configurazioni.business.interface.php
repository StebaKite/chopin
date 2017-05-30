<?php

require_once 'nexus6.main.interface.php';

interface ConfigurazioniBusinessInterface extends MainNexus6Interface {

	// Nomi

	const NESSUNO = "NS";
	const COSTI_FISSI = "CF";
	const COSTI_VARIABILI = "CV";
	const RICAVI = "RC";

	// Oggetti

	const CONTO = "Obj_conto";
	const SOTTOCONTO = "Obj_sottoconto";
	const CAUSALE = "Obj_causale";
	const CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";
	const PROGRESIVO_FATTURA = "Obj_progressivofattura";

 	const RICERCA_CONTO = "Obj_ricercaconto";
 	const MODIFICA_CONTO = "Obj_modificaconto";
 	const CANCELLA_CONTO = "Obj_cancellaconto";
 	const CREA_CONTO = "Obj_creaconto";
    const CANCELLA_SOTTOCONTO = "Obj_cancellasottoconto";
    const AGGIUNGI_SOTTOCONTO = "Obj_aggiungisottoconto";
    const TOGLI_SOTTOCONTO = "Obj_toglisottoconto";
    const ESTRAI_MASTRINO = "Obj_estraimastrino";
    const ESTRAI_PDF_MASTRINO = "Obj_estraipdfmastrino";
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

	// Actions

	const AZIONE_RICERCA_CONTO = "../configurazioni/ricercaContoFacade.class.php?modo=go";
	const AZIONE_MODIFICA_CONTO = "../configurazioni/modificaContoFacade.class.php?modo=go";
	const AZIONE_GENERA_MASTRINO = "../configurazioni/generaMastrinoContoFacade.class.php?modo=go";
	const AZIONE_ESTRAI_PDF_MASTRINO = "../configurazioni/estraiPdfMastrinoContoFacade.class.php?modo=go";
	const AZIONE_CREA_CONTO = "../configurazioni/creaContoFacade.class.php?modo=go";
	const AZIONE_RICERCA_CAUSALE = "../configurazioni/ricercaCausaleFacade.class.php?modo=go";
	const AZIONE_CREA_CAUSALE = "../configurazioni/creaCausaleFacade.class.php?modo=go";
	const AZIONE_RICERCA_PROGRESSIVO_FATTURA = "../configurazioni/ricercaProgressivoFatturaFacade.class.php?modo=go";
	const AZIONE_MODIFICA_PROGRESSIVO_FATTURA = "../configurazioni/modificaProgressivoFatturaFacade.class.php?modo=go";
	const AZIONE_CONFIGURA_CAUSALE = "../configurazioni/configuraCausaleFacade.class.php?modo=go";
	const AZIONE_MODIFICA_CAUSALE = "../configurazioni/modificaCausaleFacade.class.php?modo=go";

	// Bottoni

	const CANCELLA_SOTTOCONTO_HREF = "<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottoconto(";
	const CANCELLA_SOTTOCONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>";
	const MODIFICA_GRUPPO_SOTTOCONTO_HREF = "<td id='icons'><a class='tooltip' onclick='modificaGruppoSottoconto(";
	const MODIFICA_GRUPPO_SOTTOCONTO_ICON = ")'><li class='ui-state-default ui-corner-all' title='Cambia gruppo'><span class='ui-icon ui-icon-tag'></span></li></a></td>";

	// Errori e messaggi

 	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
 	const MSG_DA_CREAZIONE_CONTO = "messaggioCreazione";
 	const MSG_DA_GENERAZIONE_MASTRINO = "messaggioGeneraMastrino";
 	const MSG_DA_MODIFICA_CONTO = "messaggioModifica";

 	const ERRORE_CREAZIONE_CONTO = "Attenzione: conto gia' esistente!";
 	const ERRORE_CREAZIONE_CAUSALE = "Causale già esistente, inserimento fallito";
 	const GENERA_MASTRINO_OK = "Mastrino del conto generato!";
 	const REGISTRAZIONI_NON_TROVATE = "Nessuna registrazione trovata!";
 	const CREA_CONTO_OK = "Conto salvato con successo";
 	const MODIFICA_CONTO_OK = "Conto aggiornato con successo";
 	const CANCELLA_CONTO_OK = "Conto cancellato";
 	const CANCELLA_CAUSALE_OK = "Causale cancellata";
 	const CREA_CAUSALE_OK = "Causale salvata con successo";
 	const AGGIORNA_PROGRESSIVO_OK = "Progressivo fattura salvato con successo";
 	const AGGIORNA_CAUSALE_OK = "Causale salvata con successo";

	// Metodi

 	public function getInstance();
 	public function start();
 	public function go();
}

?>
