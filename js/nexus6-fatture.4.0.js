//---------------------------------------------------------------------------------				
// Fatture
//---------------------------------------------------------------------------------				

$( "#nuovo-dett-fattura-aziende" ).click(function( event ) {
	$( "#nuovo-dettaglio-fattura-aziende-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-fattura-aziende-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 510,
	height: 420,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiDettaglio();
				$( this ).dialog( "close" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

//---------------------------------------------------------------------------------				

$( "#nuovo-dett-fattura-cliente" ).click(function( event ) {
	$( "#nuovo-dettaglio-fattura-cliente-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-fattura-cliente-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 510,
	height: 420,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiDettaglio();
				$( this ).dialog( "close" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

//---------------------------------------------------------------------------------				

function cancellaDettaglioFattura(id) {
	
	$("#" + id).remove();	
	
 	var rowCount = $("#dettagli tbody tr").length;
	
	if (rowCount == 1) {
		$( "#dettagli thead tr" ).remove();		
		$( "#dettagli" ).removeClass("datiCreateSottile");
	}
	
	var c = parseInt(id.toString());
	var index = jQuery.inArray(c,indexDettInseriti);
	if (index == -1) {
		var cc = id.toString();
		var index = jQuery.inArray(cc,indexDettInseriti);
	}	
	
	if (index > -1) {
 		indexDettInseriti.splice(index, 1);
 		aggiornaIndexDettaglioInseriti(indexDettInseriti);

 		dettInseriti.splice(index, 1);				
 		aggiornaDettaglioInseriti(dettInseriti);
	}

	// Scopro il bottone nuovo dettaglio nascosto dalla funzione di creazione fattura nel caso di contributo

	$("#nuovo-dett-fattura-cliente").show();
	$("#nuovo-dett-fattura-aziende").show();
}

//---------------------------------------------------------------------------------				
