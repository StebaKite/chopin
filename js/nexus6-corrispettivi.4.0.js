//---------------------------------------------------------------------------------				
// Corrispettivi
//---------------------------------------------------------------------------------				


$( "#nuovo-dett-corrisp" ).click(function( event ) {
	$( "#nuovo-dettaglio-corrispettivo-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-corrispettivo-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo duplicazione corrispettivo
				
				var datareg = $("#datareg").val();
				var codneg = $("input[name=codneg]:checked").val();
				var conto = $("#conti").val();
				var importo = $("#importo").val();
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	if (xmlhttp.responseText == "Corrispettivo ok") {
							
							aggiungiDettaglio();
				            $( "#esitoControlloUnivocitaCorrispettivo" ).html("&nbsp;");
			        		
			        	}
			        	else {
				            $( "#esitoControlloUnivocitaCorrispettivo" ).html(xmlhttp.responseText);
			        	}
			        }		
			        
			    } 
			    xmlhttp.open("GET", "controlloCorrispettivoFacade.class.php?modo=start&datareg=" + datareg + "&codneg=" + codneg + "&conto=" + conto + "&importo=" + importo, true);
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
function cancellaCorrispettivo(idreg) {
//---------------------------------------------------------------------------------					
	$( "#idRegistrazione" ).val(idreg);
	$( "#cancella-corrispettivo-form" ).dialog( "open" );
}

$( "#cancella-corrispettivo-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
       $("#cancellaCorrispettivo").submit();				
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

$( ".selectmenuMercato" )
	.selectmenu({change:
		function() {
		
			var mercati = $("#mercati").val();
			
			if (mercati != "") {
	        	$( "#tdmercato").removeClass("inputFieldError");	
	            $( "#esitoMercato" ).val("");			
				$( "#messaggioControlloMercato" ).html("");
			}
			else {
				$("#messaggioControlloMercato").html("Dato errato");
				$("#tdmercato").addClass("inputFieldError");	
			}
		}
	})
	.selectmenu({width: 350})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------					
