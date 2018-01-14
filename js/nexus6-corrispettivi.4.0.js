//---------------------------------------------------------------------------------				
// Corrispettivi
//---------------------------------------------------------------------------------				

$( "#nuovo-corrispettivo-mercato" ).click(function( event ) {	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {    	  
			document.getElementById("nuovoCorrispettivoMercatoForm").reset();
			$("#codneg_cormer_cre option[value=' ']").prop('selected', true);
			$("#causale_cormer_cre option[value=' ']").prop('selected', true);
			$("#dettagli_cormer_cre").html("");
			$("#dettagli_cormer_cre_messaggio").html("");			
			$("#nuovo-corrispettivo-mercato-dialog").modal("show");
		}
	} 
	xmlhttp.open("GET", "creaCorrispettivoMercatoFacade.class.php?modo=start", true);
	xmlhttp.send();		
});

//---------------------------------------------------------------------------------

$("#codneg_cormer_cre").change(
	function() {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				$("#mercato_cormer_cre").html(xmlhttp.responseText);
				$("#mercato_cormer_cre").selectmenu("refresh");
			}
		}
		xmlhttp.open("GET","leggiMercatiNegozioFacade.class.php?modo=start&codneg_cormer_cre=" + this.value, true);
		xmlhttp.send();
	}
);

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuovo-corrispettivo-mercato-form").click(function() {
	$("#nuovo-dettaglio-corrispettivo-mercato-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuovo-corrispettivo-mercato-form").click(
	function() {
		
		var importo = $("#importo_cormer_cre").val();
		var importoNormalizzato = importo.trim().replace(",", ".");
		var imponibile = $("#imponibile_cormer_cre").val();
		var imponibileNormalizzato = imponibile.trim().replace(",", ".");
		var iva = $("#iva_cormer_cre").val();
		var ivaNormalizzato = iva.trim().replace(",", ".");
		
		if($('#aliquota10_cormer_cre').is(':checked')) { var aliquota = $("#aliquota10_cormer_cre").val(); }
		if($('#aliquota20_cormer_cre').is(':checked')) { var aliquota = $("#aliquota20_cormer_cre").val(); }

		var conto = $("#conti_cormer").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
		var idconto = conto.substring(0, 6);
		
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	        	var dettagliTable = xmlhttp.responseText;
        		$("#dettagli_cormer_cre").html(dettagliTable);
        		$("#dettagli_cormer_cre").show();
        		controllaDettagliRegistrazione("dettagli_cormer_cre");
        	}
	    }
	    xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoMercatoFacade.class.php?modo=go&codconto_cormer_cre=" + idconto + "&aliquota_cormer_cre=" + aliquota + "&importo_cormer_cre=" + importoNormalizzato + "&iva_cormer_cre=" + ivaNormalizzato + "&imponibile_cormer_cre=" + imponibileNormalizzato , true);
	    xmlhttp.send();				
	}
);		

//---------------------------------------------------------------------------------

$("#causale_cormer_cre").change(
	function() {
		var causale = $("#causale_cormer_cre").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_cormer").html(xmlhttp.responseText);
						$("#conti_cormer").selectmenu("refresh");
					}
				}
			xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------			

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

$("#button-ok-nuovo-corrispettivo-mercato-form").click(
	function() {
		if (validaNuovoCorrispettivoMercato()) {
			$("#testo-messaggio-successo").html("Corrispettivo mercato salvato con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#nuovoCorrispettivoMercatoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il corrispettivo non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

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
		return true;
	} else {
		return false;	
	}
}

//---------------------------------------------------------------------

function visualizzaCorrispettivoMercato(idRegistrazione)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("corrispettivo").each(
				function() {

					$("#datareg_cormer_vis").html($(this).find("datareg").text());
					$("#descreg_cormer_vis").html($(this).find("descreg").text());
					$("#causale_cormer_vis").html($(this).find("causale").text());
					$("#codneg_cormer_vis").html($(this).find("codneg").text());
					
					var mercato   = $(this).find("mercato").text();

					$("#mercato_cormer_vis").html(mercato);
					$("#dettagli_cormer_vis").html($(this).find("dettagli").text());
				}
			)

			$("#visualizza-corrispettivo-mercato-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","visualizzaCorrispettivoMercatoFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
	xmlhttp.send();
}


















// ================================================ vecchie funzioni


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
			        	document.getElementById("nuovoCorrispettivoMercato").reset();
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

$( "#nuovo-corrispettivo-negozio" ).click(function( event ) {
	$("#button-ok-nuovo-corrispettivonegozio-form").button("disable");
	$("#button-dettaglio-nuovo-corrispettivonegozio-form").button("disable");
	$("#descreg_corneg_cre").hide();
	$("#descreg_corneg_cre_label").hide();
	$("#dettagli_corneg_cre").hide();

	$("#nuovo-corrispettivo-negozio-form").dialog("open");
});

$("#nuovo-corrispettivo-negozio-form").dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-nuovo-corrispettivonegozio-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoCorrispettivoNegozio").submit();				
			}
		},
		{
			id: "button-dettaglio-nuovo-corrispettivonegozio-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-dettaglio-nuovo-corrispettivonegozio-form").button("disable");
				$("#importo_detcorneg_cre").val("");
				$("#nuovo-dettaglio-corrispettivonegozio-form").dialog( "open" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	document.getElementById("nuovoCorrispettivoNegozio").reset();
		            	$("#tddettagli_corneg_cre").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagliCorrispettivoNegozio").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaNuovoCorrispettivoNegozioFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});

//---------------------------------------------------------------------------------			

$( "#nuovo-dettaglio-corrispettivonegozio-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 580,
	height: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {

				var importo = $("#importo_corneg_cre").val();
				var importoNormalizzato = importo.trim().replace(",", ".");
				var imponibile = $("#imponibile_corneg_cre").val();
				var imponibileNormalizzato = imponibile.trim().replace(",", ".");
				var iva = $("#iva_corneg_cre").val();
				var ivaNormalizzato = iva.trim().replace(",", ".");
				
				if($('#aliquota10_corneg_cre').is(':checked')) { var aliquota = $("#aliquota10_corneg_cre").val(); }
				if($('#aliquota20_corneg_cre').is(':checked')) { var aliquota = $("#aliquota20_corneg_cre").val(); }

				var conto = $("#conti_corneg_cre").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
				var idconto = conto.substring(0, 6);
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var dettagliTable = xmlhttp.responseText;
		        		$("#dettagli_corneg_cre").html(dettagliTable);
		        		$("#dettagli_corneg_cre").show();
		        		controllaDettagliRegistrazione("tddettagli_corneg_cre","messaggioControlloDettagliCorrispettivoNegozio","descreg_corneg_cre","descreg_corneg_cre_label");
		        	}
			    }
			    xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoNegozioFacade.class.php?modo=go&codconto_corneg_cre=" + idconto + "&aliquota_corneg_cre=" + aliquota + "&importo_corneg_cre=" + importoNormalizzato + "&iva_corneg_cre=" + ivaNormalizzato + "&imponibile_corneg_cre=" + imponibileNormalizzato , true);
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
//CREA NUOVO CORRISPETTIVO NEGOZIO : controllo campi in pagina
//---------------------------------------------------------------------------------		

function validaNuovoCorrispettivoNegozio()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_corneg_cre", "tddatareg_corneg_cre", "messaggioControlloDataCorrispettivoNegozio");
	if ($("#messaggioControlloDataCorrispettivoNegozio").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_corneg_cre").val() != "") {
		if (controllaDescrizione("descreg_corneg_cre", "tddescreg_corneg_cre", "messaggioControlloDescrizioneCorrispettivoNegozio")) 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#causale_corneg_cre").val() != "") {
		controllaDettagliRegistrazione("tddettagli_corneg_cre","messaggioControlloDettagliCorrispettivoNegozio","descreg_corneg_cre","descreg_corneg_cre_label");
		if ($("#messaggioControlloCausaleCorrispettivoNegozio").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if (esito == "111") {
		$("#button-ok-nuovo-corrispettivonegozio-form").button("enable");
	} else {
		$("#button-ok-nuovo-corrispettivonegozio-form").button("disable");	
	}
}

//---------------------------------------------------------------------------------		