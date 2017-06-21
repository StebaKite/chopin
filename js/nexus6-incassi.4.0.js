//---------------------------------------------------------------------------------				
// Incassi
//---------------------------------------------------------------------------------				

$( "#nuovo-dett-incasso" ).click(function( event ) {
	$( "#nuovo-dettaglio-incasso-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-incasso-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio con codice cliente
				
				var conto = $("#conti").val();
				var cliente = $("#cliente").val();
				
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

$( "#nuovo-dett-modifica-incasso" ).click(function( event ) {
	$( "#nuovo-dettaglio-modifica-incasso-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-modifica-incasso-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio con codice cliente
				
				var conto = $("#conti").val();
				var cliente = $("#cliente").val();
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	if (xmlhttp.responseText == "Dettaglio ok") {
			        		
			        		$("#nuovoDettaglioIncasso").submit();
				            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
			        		
			        	}
			        	else {
				            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
			        	}
			        }		
			        
			    } 
			    xmlhttp.open("GET", "controlloContoDettaglioIncassoFacade.class.php?modo=start&cliente=" + cliente + "&conto=" + conto, true);
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
function cancellaIncasso(idscad,idinc) {
//---------------------------------------------------------------------------------	
	$( "#idScadenza" ).val(idscad);
	$( "#idIncasso" ).val(idinc);
	$( "#cancella-incasso-form" ).dialog( "open" );
}

$( "#cancella-incasso-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#cancellaIncasso").submit();				
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

$( ".scadenzeAperteCliente" ).change(function() {

	var descli = $("#cliente").val();
		
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $( "#select2" ).html(xmlhttp.responseText);
            $( "#select2" ).selectmenu( "refresh" );
        }
    }
    xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descli=" + descli, true);
    xmlhttp.send();			
})

//---------------------------------------------------------------------------------	

$( ".selectmenuCausaleIncassi" )
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

$(".numfatt-cliente-multiple").select2().on("change", function() {
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
function cancellaDettaglioIncasso(idconto) {
//---------------------------------------------------------------------------------		
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificainc-form" ).dialog( "open" );
}

$( "#cancella-dettaglio-modificainc-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#cancellaDettaglioIncasso").submit();				
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

