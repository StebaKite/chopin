<?php

require_once 'nexus6.main.interface.php';

interface ScadenzeBusinessInterface extends MainNexus6Interface {

	// Oggetti

	const VISUALIZZA_SCADENZA_FORNITORE = "Obj_visualizzascadenzafornitore";
	const MODIFICA_SCADENZA_FORNITORE = "Obj_modificascadenzafornitore";
	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
	const SCADENZA_CLIENTE = "Obj_scadenzacliente";
	const ESTRAI_PDF_SCADENZE_FORNITORE = "Obj_estraipdfscadenzefornitore";
	const ESTRAI_PDF_SCADENZE_CLIENTE = "Obj_estraipdfscadenzecliente";
	const CANCELLA_PAGAMENTO = "Obj_cancellapagamento";
	const REGISTRAZIONE = "Obj_registrazione";
	const LAVORO_PIANIFICATO ="Obj_lavoropianificato";

	// Actions

	const AZIONE_RICERCA_SCADENZE_FORNITORE = "../scadenze/ricercaScadenzeFornitoreFacade.class.php?modo=go";
	const AZIONE_RICERCA_SCADENZE_CLIENTE   = "../scadenze/ricercaScadenzeClienteFacade.class.php?modo=go";

	// Files XML
	
	const XML_SCADENZA_FORNITORE = "/scadenze/xml/scadenzaFornitore.xml";
	
	// Errori e messaggi

	const MSG_DA_CANCELLAZIONE = "messaggioCancellazione";
	const MSG_DA_MODIFICA = "messaggioModifica";

	// Errori e messaggi

	const CANCELLA_PAGAMENTO_OK = "Pagamento cancellato e scadenza aperta";

	// Metodi

	public function getInstance();
	public function start();
	public function go();
}

?>