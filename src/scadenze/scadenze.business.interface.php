<?php

require_once 'nexus6.main.interface.php';

interface ScadenzeBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
	const SCADENZA_CLIENTE = "Obj_scadenzacliente";
	const RICERCA_SCADENZE_FORNITORE = "Obj_ricercascadenzefornitore";
	const RICERCA_SCADENZE_CLIENTE = "Obj_ricercascadenzecliente";
	const ESTRAI_PDF_SCADENZE_FORNITORE = "Obj_estraipdfscadenzefornitore";
	const ESTRAI_PDF_SCADENZE_CLIENTE = "Obj_estraipdfscadenzecliente";
	const CANCELLA_PAGAMENTO = "Obj_cancellapagamento";
	const REGISTRAZIONE = "Obj_registrazione";
	const LAVORO_PIANIFICATO ="Obj_lavoropianificato";

	// Actions

	const AZIONE_RICERCA_SCADENZE_FORNITORE = "../scadenze/ricercaScadenzeFornitoreFacade.class.php?modo=go";
	const AZIONE_RICERCA_SCADENZE_CLIENTE   = "../scadenze/ricercaScadenzeClienteFacade.class.php?modo=go";

	// Errori e messaggi

	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const MSG_DA_MODIFICA = "messaggioModifica";
	const SCADENZA_APERTA = "00";

	// Errori e messaggi

	const CANCELLA_PAGAMENTO_OK = "Pagamento cancellato e scadenza aperta";

	// Metodi

	public function getInstance();
	public function start();
	public function go();
}

?>