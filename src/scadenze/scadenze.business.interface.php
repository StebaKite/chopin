<?php

require_once 'nexus6.main.interface.php';

interface ScadenzeBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
	const RICERCA_SCADENZE_FORNITORE = "Obj_ricercascadenzefornitore";

	// Actions

	const AZIONE_RICERCA_SCADENZE_FORNITORE = "../scadenze/ricercaScadenzeFornitoreFacade.class.php?modo=go";

	// Errori e messaggi

	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const MSG_DA_MODIFICA = "messaggioModifica";

	// Metodi

	public function getInstance();
	public function start();
	public function go();
}

?>