<?php

require_once 'nexus6.main.interface.php';

interface UtilityComponentInterface extends MainNexus6Interface {

	// Oggetti

	const PDF = "Obj_pdf";
	const PDF_SCADENZE = "Obj_pdfscadenze";
	const PDF_CONTI = "Obj_pdfconti";

	// Altre costanti

	const EURO = 'EURO';
	const SCADENZA_APERTA = '00';
	const SCADENZA_CHIUSA = '10';
	const SCADENZA_RIMANDATA = '02';

	const SCADENZA_DAPAGARE = "Da Pagare";
	const SCADENZA_PAGATA = "Pagato";
	const SCADENZA_POSTICIPATA = "Posticipato";

	const CONTO_IN_DARE = "Dare";
	const CONTO_IN_AVERE = "Avere";




}

?>