<?php

interface MainNexus6Interface {

	// Negozi

	const NEGOZIO_VILLA = "Villa D'Adda";
	const NEGOZIO_BREMBATE = "Brembate";
	const NEGOZIO_TREZZO = "Trezzo";

	// Costanti

	const TESTATA   		= "testataPagina";
	const PIEDE     		= "piedePagina";
	const ERRORE    		= "messaggioErrore";
	const INFO      		= "messaggioInfo";
	const AMBIENTE  		= "ambiente";
	const MESSAGGIO 		= "messaggio";
	const MSG               = "divmsg";
	const AZIONE    		= "azione";
	const TIP_CONFERMA 		= "confermaTip";
	const TITOLO_PAGINA		= "titoloPagina";
	const LOGO				= "logo";
	const CREATORE			= "creator";
	const NEXUS6			= "Nexus6";
	const PDF_TITLE			= "title";
	const PDF_SOTTOTITOLO	= "title1";

	// Messaggi

	const EMPTYSTRING = "";
	const CAMPO_VUOTO = "&ndash;&ndash;&ndash;";
	const DATA_ALTA = "31/12/9999";
	const SELECT_THIS_ITEM = "selected";
	const VILLA = "VIL";
	const TREZZO = "TRE";
	const BREMBATE = "BRE";

	// Costanti comuni

	const EURO = 'EURO';
	const SCADENZA_APERTA = "00";
	const SCADENZA_CHIUSA = "10";
	const SCADENZA_RIMANDATA = '02';
	const SCADENZA_SOSPESA = "  ";

	const SCADENZA_DA_PAGARE = "Da Pagare";
	const SCADENZA_PAGATA = "Pagato";
	const SCADENZA_POSTICIPATA = "Posticipato";

	const CONTO_IN_DARE = "Dare";
	const CONTO_IN_AVERE = "Avere";
	
	// Bottoni
	
	const VISUALIZZA_ICON = ")'><span class='glyphicon glyphicon-eye-open'></span></a>";
	const MODIFICA_ICON = ")'><span class='glyphicon glyphicon-edit'></span></a>";
	const CANCELLA_ICON = ")'><span class='glyphicon glyphicon-trash'></span></a>";
	const OK_ICON = "<span class='glyphicon glyphicon-ok'></span>";
	const LISTA_ICON = ")'><span class='glyphicon glyphicon-list-alt'></span></a>";
	const CONFIGURA_ICON = ")'><span class='glyphicon glyphicon-wrench'></span></a>";
	const INCLUDI_ICON = ")'><span class='glyphicon glyphicon-triangle-left'></span></a>";
	const ESCLUDI_ICON = ")'><span class='glyphicon glyphicon-triangle-right'></span></a>";
	
	// Messaggi d'errore comuni
	
	const ERRORE_LETTURA = "Errore fatale durante la lettura dei dati!!";
	
}

?>