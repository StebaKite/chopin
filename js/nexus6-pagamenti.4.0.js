//---------------------------------------------------------------------------------				
// Pagamenti
//---------------------------------------------------------------------------------				


$( "#nuovo-dett-pagam" ).click(function( event ) {
	$( "#nuovo-dettaglio-pagamento-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-pagamento-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio con codice fornitore
				
				var conto = $("#conti").val();
				var fornitore = $("#fornitore").val();
				
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

$( "#nuovo-dett-modifica-pagamento" ).click(function( event ) {
	$( "#nuovo-dettaglio-modifica-pagamento-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-modifica-pagamento-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio con codice fornitore
				
				var conto = $("#conti").val();
				var fornitore = $("#fornitore").val();
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	if (xmlhttp.responseText == "Dettaglio ok") {
			        		
			                $("#nuovoDettaglioPagamento").submit();				
				            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
			        		
			        	}
			        	else {
				            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
			        	}
			        }		
			        
			    } 
			    xmlhttp.open("GET", "controlloContoDettaglioPagamentoFacade.class.php?modo=start&fornitore=" + fornitore + "&conto=" + conto, true);
			    xmlhttp.send();				

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
function cancellaPagamento(idscad,idpag) {
//---------------------------------------------------------------------------------	
	$( "#idScadenza" ).val(idscad);
	$( "#idPagamento" ).val(idpag);
	$( "#cancella-pagamento-form" ).dialog( "open" );
}

$( "#cancella-pagamento-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
       $("#cancellaPagamento").submit();				
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

$( ".scadenzeAperteFornitore" ).change(function() {
	
	var desforn = $("#fornitore").val();
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $( "#select2" ).html(xmlhttp.responseText);
            $( "#select2" ).selectmenu( "refresh" );
        }
    }
    xmlhttp.open("GET", "ricercaScadenzeAperteFornitoreFacade.class.php?modo=start&desforn=" + desforn, true);
    xmlhttp.send();		
});

//---------------------------------------------------------------------------------	

$( ".selectmenuCausalePagamenti" )
	.selectmenu({change:
		function(){
			var causale = $("#causale").val();
			
			if (causale != "") {
	        	$( "#tdcausale").removeClass("inputFieldError");	
	            $( "#esitoCausale" ).val("");			
				$( "#messaggioControlloCausale" ).html("");
			}
			else {
				$("#messaggioControlloCausale").html("Dato errato");
				$("#tdcausale").addClass("inputFieldError");	
			}
		
			var xmlhttp = new XMLHttpRequest();
	        xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	                $( "#conti" ).html(xmlhttp.responseText);
	                $( "#conti" ).selectmenu( "refresh" );
	            }
	        }
	        xmlhttp.open("GET", "leggiContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
	        xmlhttp.send();			
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------	

$(".numfatt-multiple").select2().on("change", function() {
	var numfatt = $("#select2").val();
	if (numfatt == undefined) {
		$("#messaggioControlloNumeroFattura").html("Dato errato");
		$("#tdnumfatt").addClass("inputFieldError");			
	}
	else {
		if (numfatt .length > 0) {
	    	$( "#tdnumfatt").removeClass("inputFieldError");	
	        $( "#esitoNumfatt" ).val("");			
			$( "#messaggioControlloNumeroFattura" ).html("");
		}
		else {
			$("#messaggioControlloNumeroFattura").html("Dato errato");
			$("#tdnumfatt").addClass("inputFieldError");	
		}		
	}
})	

//---------------------------------------------------------------------------------	
function cancellaDettaglioPagamento(idconto) {
//---------------------------------------------------------------------------------	
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificapag-form" ).dialog( "open" );
}

$( "#cancella-dettaglio-modificapag-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
         $("#cancellaDettaglioPagamento").submit();				
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
