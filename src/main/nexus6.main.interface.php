<?php

interface MainNexus6Interface {

	// Costanti

	const TESTATA   	= "testataPagina";
	const PIEDE     	= "piedePagina";
	const ERRORE    	= "messaggioErrore";
	const INFO      	= "messaggioInfo";
	const AMBIENTE  	= "ambiente";
	const MESSAGGIO 	= "messaggio";
	const AZIONE    	= "azione";
	const TIP_CONFERMA 	= "confermaTip";
	const TITOLO_PAGINA	= "titoloPagina";

	// Messaggi

	const EMPTYSTRING = "";
	const CAMPO_VUOTO = "&ndash;&ndash;&ndash;";
	const DATA_ALTA = "31/12/9999";
	const SELECT_THIS_ITEM = "selected";
	const VILLA = "VIL";
	const TREZZO = "TRE";
	const BREMBATE = "BRE";
	const ERRORE_LETTURA = "Errore fatale durante la lettura dei dati";
	const ERRORE_SCRITTURA = "Errore fatale durante la scrittura dei dati";

	// Bottoni

	const BOTTONE_ESTRAI_PDF = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
}

?>