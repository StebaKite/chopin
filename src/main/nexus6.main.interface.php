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

	const SCADENZA_DAPAGARE = "Da Pagare";
	const SCADENZA_PAGATA = "Pagato";
	const SCADENZA_POSTICIPATA = "Posticipato";

	const CONTO_IN_DARE = "Dare";
	const CONTO_IN_AVERE = "Avere";

	// Bottoni

	const BOTTONE_ESTRAI_PDF = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
}

?>