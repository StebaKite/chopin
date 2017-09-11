//---------------------------------------------------------------------------------				
// Corrispettivi
//---------------------------------------------------------------------------------				

$( "#nuovo-corrispettivo-mercato" ).click(function( event ) {
	$("#button-ok-nuovo-corrispettivomercato-form").button("disable");
	$("#button-dettaglio-nuovo-corrispettivomercato-form").button("disable");
	$("#descreg_cormer_cre").hide();
	$("#descreg_cormer_cre_label").hide();
	$("#dettagli_cormer_cre").hide();

	$("#nuovo-corrispettivo-mercato-form").dialog("open");
});

$("#nuovo-corrispettivo-mercato-form").dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-nuovo-corrispettivomercato-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoCorrispettivoMercato").submit();				
			}
		},
		{
			id: "button-dettaglio-nuovo-corrispettivomercato-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-dettaglio-nuovo-corrispettivomercato-form").button("disable");
				$("#importo_detcormer_cre").val("");
				$("#nuovo-dettaglio-corrispettivomercato-form").dialog( "open" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	document.getElementById("nuovoPagamento").reset();
		            	$("#tddettagli_cormer_cre").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagliCorrispettivoMercato").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaNuovoCorrispettivoMercatoFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});

//---------------------------------------------------------------------------------			

$( "#nuovo-dettaglio-corrispettivomercato-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 580,
	height: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {

				var importo = $("#importo_cormer_cre").val();
				var importoNormalizzato = importo.trim().replace(",", ".");
				var imponibile = $("#imponibile_cormer_cre").val();
				var imponibileNormalizzato = imponibile.trim().replace(",", ".");
				var iva = $("#iva_cormer_cre").val();
				var ivaNormalizzato = iva.trim().replace(",", ".");
				
				if($('#aliquota10_cormer_cre').is(':checked')) { var aliquota = $("#aliquota10_cormer_cre").val(); }
				if($('#aliquota20_cormer_cre').is(':checked')) { var aliquota = $("#aliquota20_cormer_cre").val(); }

				var conto = $("#conti_cormer_cre").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
				var idconto = conto.substring(0, 6);
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var dettagliTable = xmlhttp.responseText;
		        		$("#dettagli_cormer_cre").html(dettagliTable);
		        		$("#dettagli_cormer_cre").show();
		        		controllaDettagliRegistrazione("tddettagli_cormer_cre","messaggioControlloDettagliCorrispettivoMercato","descreg_cormer_cre","descreg_cormer_cre_label");
		        	}
			    }
			    xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoMercatoFacade.class.php?modo=go&codconto_cormer_cre=" + idconto + "&aliquota_cormer_cre=" + aliquota + "&importo_cormer_cre=" + importoNormalizzato + "&iva_cormer_cre=" + ivaNormalizzato + "&imponibile_cormer_cre=" + imponibileNormalizzato , true);
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

$( ".selectmenuCausaleCorMerCre" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_cormer_cre").val();
			
			if (causale != "") {
	        	$( "#tdcausale_cormer_cre").removeClass("inputFieldError");	
				$( "#messaggioControlloCausaleCorrispettivoMercato" ).html("");
				
				var xmlhttp = new XMLHttpRequest();
		        xmlhttp.onreadystatechange = function() {
		            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		                $("#conti_cormer_cre").html(xmlhttp.responseText);
		            	$("#button-dettaglio-nuovo-corrispettivomercato-form").button("enable");	                
		            	validaNuovoCorrispettivoMercato();
		            }
		        }
		        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
		        xmlhttp.send();			
								
			}
			else {
				$("#tdcausale_cormer_cre").addClass("inputFieldError");	
				$("#messaggioControlloCausaleCorrispettivoMercato").html("Dato errato");
				$("#button-dettaglio-nuovo-corrispettivomercato-form").button("disable");	                
			}
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------

$('input[type=radio][name=codneg_cormer_cre]').change(function() {
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $( "#mercati_cormer_cre" ).html(xmlhttp.responseText);
            $( "#mercati_cormer_cre" ).selectmenu( "refresh" );
        }
    }
    xmlhttp.open("GET", "leggiMercatiNegozioFacade.class.php?modo=start&codneg_cormer_cre=" + this.value, true);
    xmlhttp.send();			
});

function calcolaImponibile(campoImporto, campoAliquota, campoImponibile, campoIva, msgSquadratura)
{	
	var importo = $("#" + campoImporto).val();
	var importoNormalizzato = importo.trim().replace(",", ".");
	
	var aliquota = $("input[name=" + campoAliquota + "]:checked").val();
	
	var imponibile = importoNormalizzato / aliquota;
	var imponibileArrotondato = imponibile.toFixed(2);
	
	$("#" + campoImponibile).val(imponibileArrotondato);
	
	if (aliquota == "1.10") {
		var iva = imponibileArrotondato * 0.1;		
	}
	else {
		var iva = imponibileArrotondato * 0.22;
	}
	
	var ivaArrotondata = iva.toFixed(2);
	$("#" + campoIva).val(ivaArrotondata); 
	
	// Breve controllo di quadratura degli importi
	
	var sommaImportiCalcolati = parseFloat(imponibileArrotondato) + parseFloat(ivaArrotondata);
	var sommaImportiCalcolatiArrotondato = sommaImportiCalcolati.toFixed(2);
	
	var importoSquadratura = importoNormalizzato - parseFloat(sommaImportiCalcolatiArrotondato);
	
	if (importoSquadratura != 0) {
		$("#" + msgSquadratura).html("ATTENZIONE, squadratura di &euro; " + importoSquadratura.toFixed(2) + " Correggi manualmente");
	}
	else {
		$("#" + msgSquadratura).html("");
	}
}

//---------------------------------------------------------------------------------		
//CREA NUOVO CORRISPETTIVO MERCATO : controllo campi in pagina
//---------------------------------------------------------------------------------		

function validaNuovoCorrispettivoMercato()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_cormer_cre", "tddatareg_cor_cre", "messaggioControlloDataCorrispettivoMercato");
	if ($("#messaggioControlloDataCorrispettivoMercato").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_cormer_cre").val() != "") {
		if (controllaDescrizione("descreg_cormer_cre", "tddescreg_cormer_cre", "messaggioControlloDescrizioneCorrispettivoMercato")) 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#causale_cormer_cre").val() != "") {
		controllaDettagliRegistrazione("tddettagli_cormer_cre","messaggioControlloDettagliCorrispettivoMercato","descreg_cormer_cre","descreg_cormer_cre_label");
		if ($("#messaggioControlloCausaleCorrispettivoMercato").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if (esito == "111") {
		$("#button-ok-nuovo-corrispettivomercato-form").button("enable");
	} else {
		$("#button-ok-nuovo-corrispettivomercato-form").button("disable");	
	}
}

//---------------------------------------------------------------------------------		









//---------------------------------------------------------------------------------			
// Vecchie funzioni
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

$( ".selectmenuContiCor" )
	.selectmenu({change:
		function() {
		}
	})
	.selectmenu({width: 350})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------					
