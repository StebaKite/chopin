//---------------------------------------------------------------------------------				
// Registrazioni
//---------------------------------------------------------------------------------				

//---------------------------------------------------------------------------------				
//Dettaglio Registrazione
//---------------------------------------------------------------------------------
$( "#nuovo-dett" ).click(function( event ) {
	$( "#nuovo-dettaglio-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio
				
				var conto = $("#conti").val();
				var fornitore = $("#fornitore").val();
				var cliente = $("#cliente").val();

				// Fornitore
				
				if (fornitore != "") {

					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				        	if (xmlhttp.responseText == "Dettaglio ok") {
								
								aggiungiDettaglio();
					            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
				        		
				        	}
				        	else {
					            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
				        	}
				        }		
				        
				    } 
				    xmlhttp.open("GET", "controlloContoDettaglioPagamentoFacade.class.php?modo=start&fornitore=" + fornitore + "&conto=" + conto, true);
				    xmlhttp.send();
				}

				// Cliente
				
				else if (cliente != "") {

					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				        	if (xmlhttp.responseText == "Dettaglio ok") {
								
								aggiungiDettaglio();
					            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
				        		
				        	}
				        	else {
					            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
				        	}
				        }		
				        
				    } 
				    xmlhttp.open("GET", "controlloContoDettaglioIncassoFacade.class.php?modo=start&cliente=" + cliente + "&conto=" + conto, true);
				    xmlhttp.send();
				}
				else {
					aggiungiDettaglio();
				}
				
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

$( "#nuova-data-scad" ).click(function( event ) {
	$( "#datascad" ).val("");	
	$( "#nuova-data-scadenza-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuova-data-scadenza-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 150,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiScadenzaSupplementare();
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

$( "#nuovo-dett-modificareg" ).click(function( event ) {
	$( "#nuovo-dettaglio-modificareg-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio
				
				var conto = $("#conti").val();
				var fornitore = $("#fornitore").val();
				var cliente = $("#cliente").val();

				// Fornitore
				
				if (fornitore != "") {

					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				        	if (xmlhttp.responseText == "Dettaglio ok") {
								
					            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
				                $("#nuovoDettaglio").submit();				
				        		
				        	}
				        	else {
				        		$( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
				        	}
				        }		
				        
				    } 
				    xmlhttp.open("GET", "controlloContoDettaglioPagamentoFacade.class.php?modo=start&fornitore=" + fornitore + "&conto=" + conto, true);
				    xmlhttp.send();
				}

				// Cliente
				
				else if (cliente != "") {

					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				        	if (xmlhttp.responseText == "Dettaglio ok") {
								
					            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
				                $("#nuovoDettaglio").submit();				
				        		
				        	}
				        	else {
					            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
				        	}
				        }		
				        
				    } 
				    xmlhttp.open("GET", "controlloContoDettaglioIncassoFacade.class.php?modo=start&cliente=" + cliente + "&conto=" + conto, true);
				    xmlhttp.send();
				}
				else {
	                $("#nuovoDettaglio").submit();				
				}
				
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

$( "#nuova-scad-modificareg" ).click(function( event ) {
	$( "#nuova-scadenza-modificareg-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuova-scadenza-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 150,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
           $("#nuovaScadenza").submit();				
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

function cancellaDettaglio(idconto) {
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificareg-form" ).dialog( "open" );
}
$( "#cancella-dettaglio-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
           $("#cancellaDettaglio").submit();				
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

function cancellaRegistrazione(idreg) {	
	$( "#idRegistrazione" ).val(idreg);
	$( "#cancella-registrazione-form" ).dialog( "open" );
}

$( "#cancella-registrazione-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
         $("#cancellaRegistrazione").submit();				
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
function cancellaScadenza(idscadenza) {
//---------------------------------------------------------------------------------	
	$( "#idScadenzaRegistrazione" ).val(idscadenza);
	$( "#cancella-scadenza-modificareg-form" ).dialog( "open" );
}

$( "#cancella-scadenza-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
         $("#cancellaScadenza").submit();				
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

$( "#fornitore" ).change(function() {
	
	var desfornitore = $("#fornitore").val();
	var datareg = $("#datareg").val();
	var form = $("#pagamentoForm").val();
	
	if (desfornitore != "") {

		if (form == "PAGAMENTO") {
			
			var xmlhttp = new XMLHttpRequest();
		    xmlhttp.onreadystatechange = function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		            $( "#select2" ).html(xmlhttp.responseText);
		            $( "#select2" ).selectmenu( "refresh" );
		        }
		    }
		    xmlhttp.open("GET", "ricercaScadenzeAperteFornitoreFacade.class.php?modo=start&desforn=" + desfornitore, true);
		    xmlhttp.send();		
		}
		else {
			
			/**
			 * Data scadenza
			 */			
			var xmlhttp = new XMLHttpRequest();
	        xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	                $( "#datascad" ).val(xmlhttp.responseText);
	            	$( "#tddatascad").removeClass("inputFieldError");	
	                $( "#esitoDatascad" ).val("");			
	        		$( "#messaggioControlloDataScadenza" ).html("");	                
	            }
	        }
	        xmlhttp.open("GET", "calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore=" + desfornitore + "&datareg=" + datareg, true);
	        xmlhttp.send();						
		}		
	}
});

//---------------------------------------------------------------------------------	

$( "#fornitore_regrap" ).change(function() {
	
	var desfornitore = $("#fornitore_regrap").val();
	
	if (desfornitore != "") {
		/**
		 * Genero i dettagli della registrazione
		 */
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			$( "#dettagli" ).html(xmlhttp.responseText);	                
        }
	}
	xmlhttp.open("GET", "aggiungiFornitoreDettagliRegistrazioneFacade.class.php?modo=start&desfornitore=" + desfornitore, true);
	xmlhttp.send();						
	}		
});

//---------------------------------------------------------------------------------	

$( "#cliente_regrap" ).change(function() {
	
	var descliente = $("#cliente_regrap").val();
	
	if (descliente != "") {
		/**
		 * Genero i dettagli della registrazione
		 */
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
    		$( "#dettagli" ).html(xmlhttp.responseText);	                
        }
    }
	xmlhttp.open("GET", "aggiungiClienteDettagliRegistrazioneFacade.class.php?modo=start&descliente=" + descliente, true);
	xmlhttp.send();						
	}		
});

//---------------------------------------------------------------------------------	
/**
 * Il controllo sulla data registrazione verificha che la data immessa cada all'interno
 * di uno dei mesi in linea. I mesi in linea coincidono con le date pianificate di riporto saldo
 * 
 */
$( "#datareg" ).change(function() {
	
	var datareg = $("#datareg").val();

	if (datareg != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            $( "#messaggioControlloDataRegistrazione" ).html(xmlhttp.responseText);
	            $( "#esitoControlloDataRegistrazione" ).val(xmlhttp.responseText);
	            
	            if (xmlhttp.responseText != "") 
	            	$("#tddatareg").addClass("inputFieldError");			            	
	            else {
	            	$( "#tddatareg").removeClass("inputFieldError");	
	                $( "#esitoControlloDataRegistrazione" ).val("");
	            }
	            
	        }
	    }
	    xmlhttp.open("GET", "controllaDataRegistrazioneFacade.class.php?modo=start&datareg=" + datareg, true);
	    xmlhttp.send();		
	}
	else {
        $( "#messaggioControlloDataRegistrazione" ).html("Dato errato");
        $( "#esitoControlloDataRegistrazione" ).val("Dato errato");		
    	$( "#tddatareg" ).addClass("inputFieldError");			            	
	}
});

//---------------------------------------------------------------------------------	

$( "#cliente" ).change(function() {
	
	var descliente = $("#cliente").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $( "#tipoadd" ).val(xmlhttp.responseText);
            }
        }
        xmlhttp.open("GET", "prelevaTipoAddebitoClienteFacade.class.php?modo=start&descliente=" + descliente, true);
        xmlhttp.send();			
	}
})

//---------------------------------------------------------------------------------	

$( ".selectmenuCausale" )
	.selectmenu({width: 350})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------	

$('#numeroFatturaFornitore').change(function() {
	var fornitore = $("#fornitore").val();
	var numfatt = $("#numfatt").val();
	var causale = $("#causale").val();
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $( "#esitoControlloNumeroFattura" ).html(xmlhttp.responseText);
        }
    } 
    xmlhttp.open("GET", "cercaFatturaFornitoreFacade.class.php?modo=start&idfornitore=" + fornitore + "&numfatt=" + numfatt, true);
    xmlhttp.send();				
});

//---------------------------------------------------------------------------------	

function cancellaScadenzaSupplementarePagina(dataScadenza) {
	
	$("#" + dataScadenza).remove();	
	
 	var rowCount = $("#scadenzesuppl tbody tr").length;
	
	if (rowCount == 0) {
		$( "#scadenzesuppl thead tr" ).remove();		
		$( "#scadenzesuppl" ).removeClass("datiCreateSottile");
	}

	var c = parseInt(dataScadenza.toString());
	var index = jQuery.inArray(c,indexScadenzeInserite);
	if (index == -1) {
		var cc = dataScadenza.toString();
		var index = jQuery.inArray(cc,indexScadenzeInserite);
	}	
	
	if (index > -1) {
		indexScadenzeInserite.splice(index, 1);
		aggiornaIndexScadenzeInserite(indexScadenzeInserite);

		scadenzeInserite.splice(index, 1);				
		aggiornaScadenzeInserite(scadenzeInserite);
	}	
}

//---------------------------------------------------------------------------------	

function cancellaDettaglioPagina(idconto) {
	
	$("#" + idconto).remove();	
	
 	var rowCount = $("#dettagli tbody tr").length;
	
	if (rowCount == 1) {
		$( "#dettagli thead tr" ).remove();		
		$( "#dettagli" ).removeClass("display");
	}
	
	var c = parseInt(idconto.toString());
	var index = jQuery.inArray(c,indexDettInseriti);
	if (index == -1) {
		var cc = idconto.toString();
		var index = jQuery.inArray(cc,indexDettInseriti);
	}	
	
	if (index > -1) {
 		indexDettInseriti.splice(index, 1);
 		aggiornaIndexDettaglioInseriti(indexDettInseriti);

 		dettInseriti.splice(index, 1);				
 		aggiornaDettaglioInseriti(dettInseriti);
	}
}

//---------------------------------------------------------------------------------	

function cancellaDettaglioPagina(idconto) {
	
	$("#" + idconto).remove();	
	
 	var rowCount = $("#dettagli tbody tr").length;
	
	if (rowCount == 1) {
		$( "#dettagli thead tr" ).remove();		
		$( "#dettagli" ).removeClass("display");
	}
	
	var c = parseInt(idconto.toString());
	var index = jQuery.inArray(c,indexDettInseriti);
	if (index == -1) {
		var cc = idconto.toString();
		var index = jQuery.inArray(cc,indexDettInseriti);
	}	
	
	if (index > -1) {
 		indexDettInseriti.splice(index, 1);
 		aggiornaIndexDettaglioInseriti(indexDettInseriti);

 		dettInseriti.splice(index, 1);				
 		aggiornaDettaglioInseriti(dettInseriti);
	}
}

//---------------------------------------------------------------------------------	

function cancellaDettaglioPagina(idconto) {
	
	$("#" + idconto).remove();	
	
 	var rowCount = $("#dettagli tbody tr").length;
	
	if (rowCount == 1) {
		$( "#dettagli thead tr" ).remove();		
		$( "#dettagli" ).removeClass("display");
	}
	
	var c = parseInt(idconto.toString());
	var index = jQuery.inArray(c,indexDettInseriti);
	if (index == -1) {
		var cc = idconto.toString();
		var index = jQuery.inArray(cc,indexDettInseriti);
	}	
	
	if (index > -1) {
 		indexDettInseriti.splice(index, 1);
 		aggiornaIndexDettaglioInseriti(indexDettInseriti);

 		dettInseriti.splice(index, 1);				
 		aggiornaDettaglioInseriti(dettInseriti);
	}
}
