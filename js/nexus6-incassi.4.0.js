//---------------------------------------------------------------------------------				
// Incassi
//---------------------------------------------------------------------------------				

$( "#nuovo-incasso" ).click(function( event ) {	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {    	  
			document.getElementById("nuovoIncassoForm").reset();
			$("#codneg_inc_cre option[value=' ']").prop('selected', true);
			$("#causale_inc_cre option[value=' ']").prop('selected', true);
			$("#scadenze_aperte_inc_cre").html("");
			$("#dettagli_inc_cre").html("");
			$("#dettagli_inc_cre_messaggio").html("");			
			$("#nuovo-incasso-dialog").modal("show");
		}
	} 
	xmlhttp.open("GET", "creaIncassoFacade.class.php?modo=start", true);
	xmlhttp.send();		
});

//---------------------------------------------------------------------------------	

$( "#cliente_inc_cre" ).keyup(function() {

	var descliente = $("#cliente_inc_cre").val();
	var codnegozio = $("#codneg_inc_cre").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				var parser = new DOMParser();
				var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
				
				$(xmldoc).find("scadenzecliente").each(
					function() {
						$("#scadenze_chiuse_inc_cre").html($(this).find("scadenzeincassate").text());
						$("#scadenze_aperte_inc_cre").html($(this).find("scadenzedaincassare").text());
					}
	        	)
	        }
	    }
	    xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente_inc_cre=" + descliente + "&codnegozio_inc_cre=" + codnegozio, true);
	    xmlhttp.send();
	}
})		

//---------------------------------------------------------------------------------	

$( "#cliente_inc_mod" ).keyup(function() {

	var descliente = $("#cliente_inc_mod").val();
	var codnegozio = $("#codneg_inc_mod").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				var parser = new DOMParser();
				var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
				
				$(xmldoc).find("scadenzecliente").each(
					function() {
						$("#scadenze_chiuse_inc_mod").html($(this).find("scadenzeincassate").text());
						$("#scadenze_aperte_inc_mod").html($(this).find("scadenzedaincassare").text());
					}
	        	)
	        }
	    }
	    xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente_inc_mod=" + descliente + "&codnegozio_inc_mod=" + codnegozio, true);
	    xmlhttp.send();
	}
})		

//---------------------------------------------------------------------------------		

function aggiungiFatturaIncassata(idScadenza,idTableAperte,idTableChiuse)
{
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {	
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("scadenzecliente").each(
				function() {
					$("#" + idTableChiuse).html($(this).find("scadenzeincassate").text());
					$("#" + idTableAperte).html($(this).find("scadenzedaincassare").text());
				}
        	)
        }
    }
    xmlhttp.open("GET", "aggiungiFatturaIncassataFacade.class.php?modo=start&idscadcli=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
    xmlhttp.send();		
}

//---------------------------------------------------------------------------------		

function rimuoviFatturaIncassata(idScadenza,idTableAperte,idTableChiuse)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{	
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("scadenzecliente").each(
				function() {
					$("#" + idTableChiuse).html($(this).find("scadenzeincassate").text());
					$("#" + idTableAperte).html($(this).find("scadenzedaincassare").text());
				}
        	)
		}
	}
	xmlhttp.open("GET", "rimuoviFatturaIncassateFacade.class.php?modo=start&idscadcli=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
	xmlhttp.send();		
}

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuovo-incasso-form").click(function() {
	$("#nuovo-dettaglio-incasso-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-incasso-form").click(function() {
	$("#nuovo-dettaglio-modifica-incasso-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuovo-incasso-form").click(
	function() {

		var D_A = $("#newsegnodett_inc_cre").val();
	
		// tolgo eventuali virgole nella descrizione del conto
		
		var conto = $("#conti_inc").val().replace(",",".");
		var idconto = conto.substring(0, 6);
		
		// normalizzo la virgola dell'importo
		
		var importo = $("#newimpdett_inc_cre").val();
		var importoNormalizzato = importo.trim().replace(",", ".");
	
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = 
			function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var sottocontiTable = xmlhttp.responseText;
					$("#dettagli_inc_cre").html(sottocontiTable);
					controllaDettagliRegistrazione("dettagli_inc_cre");
				}
			}
		xmlhttp.open("GET","aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto="	+ conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
		xmlhttp.send();
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-incasso-form").click(
	function() {

		var D_A = $("#newsegnodett_inc_mod").val();
	
		// tolgo eventuali virgole nella descrizione del conto
		
		var conto = $("#conti_inc-mod").val().replace(",",".");
		var idconto = conto.substring(0, 6);
		
		// normalizzo la virgola dell'importo
		
		var importo = $("#newimpdett_inc_mod").val();
		var importoNormalizzato = importo.trim().replace(",", ".");
	
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = 
			function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var sottocontiTable = xmlhttp.responseText;
					$("#dettagli_inc_mod").html(sottocontiTable);
					controllaDettagliRegistrazione("dettagli_inc_mod");
				}
			}
		xmlhttp.open("GET","aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto="	+ conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
		xmlhttp.send();
	}
);		

//---------------------------------------------------------------------------------

$("#causale_inc_cre").change(
	function() {
		var causale = $("#causale_inc_cre").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_inc").html(xmlhttp.responseText);
						$("#conti_inc").selectmenu("refresh");
					}
				}
			xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_inc_mod").change(
	function() {
		var causale = $("#causale_inc_mod").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_inc_mod").html(xmlhttp.responseText);
						$("#conti_inc_mod").selectmenu("refresh");
					}
				}
			xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-incasso-form").click(
	function() {
		if (validaNuovoIncasso()) {
			$("#testo-messaggio-successo").html("Incasso salvato con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#nuovoIncassoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore l'incasso non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-incasso-form").click(
	function() {
		if (validaModificaIncasso()) {
			$("#testo-messaggio-successo").html("Incasso salvato con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#modificaIncassoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore l'incasso non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------		
// CREA NUOVO INCASSO : validazione dati immessi
//---------------------------------------------------------------------------------		

function validaNuovoIncasso()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_inc_cre", "tddatareg_inc_cre", "messaggioControlloDataIncasso");
	if ($("#messaggioControlloDataIncasso").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_inc_cre").val() != "") {
		if (controllaDescrizione("descreg_inc_cre", "tddescreg_inc_cre", "messaggioControlloDescrizioneIncasso")) 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#causale_inc_cre").val() != "") {
		controllaDettagliRegistrazione("tddettagli_inc_cre","messaggioControlloDettagliIncasso","descreg_inc_cre","descreg_inc_cre_label");
		if ($("#messaggioControlloDettagliIncasso").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if (esito == "111") { return true; }
	else { return false; }	
}

//---------------------------------------------------------------------

function visualizzaIncasso(idIncasso)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("incasso").each(
				function() {

					$("#datareg_inc_vis").html($(this).find("datareg").text());
					$("#descreg_inc_vis").html($(this).find("descreg").text());
					$("#causale_inc_vis").html($(this).find("causale").text());
					$("#codneg_inc_vis").html($(this).find("codneg").text());
					
					var cliente   = $(this).find("cliente").text();

					$("#cliente_inc_vis").html(cliente);					
					$("#scadenze_incassate_inc_vis").html($(this).find("scadenzeincassate").text());					
					$("#dettagli_inc_vis").html($(this).find("dettagli").text());
				}
			)

			$("#visualizza-incasso-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","visualizzaIncassoFacade.class.php?modo=start&idinc=" + idIncasso, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------		

function modificaIncasso(idIncasso)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("incasso").each(
				function() {

					$("#datareg_inc_mod").val($(this).find("datareg").text());
					$("#descreg_inc_mod").val($(this).find("descreg").text());
					$("#causale_inc_mod").val($(this).find("causale").text());

					var negozio = $(this).find("codneg").text();
					$("#codneg_inc_mod option[value='" + negozio + "']").prop('selected', true);
					
					var cliente   = $(this).find("cliente").text();
					$("#cliente_inc_mod").val(cliente);
					
					$("#scadenze_chiuse_inc_mod").html($(this).find("scadenzeincassate").text());					
					$("#scadenze_aperte_inc_mod").html($(this).find("scadenzedaincassare").text());					
					$("#dettagli_inc_mod").html($(this).find("dettagli").text());
					$("#conti_inc_mod").html($(this).find("conti").text());
				}
			)
			$("#modifica-incasso-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "modificaIncassoFacade.class.php?modo=start&idinc=" + idIncasso, true);
	xmlhttp.send();		
}



















//============================================================== vecchie funzioni




$("#nuovo-incasso-form").dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-nuovo-incasso-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoIncasso").submit();				
			}
		},
		{
			id: "button-dettaglio-nuovo-incasso-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-dettaglio-nuovo-incasso-form").button("disable");
				$("#importo_detinc_cre").val("");
				$("#nuovo-dettaglio-incasso-form").dialog( "open" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	document.getElementById("nuovoIncasso").reset();
			        	$("#select2").select2("val", "");			        	
		            	$("#tddettagli_inc_cre").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagliIncasso").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaNuovoIncassoFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});


$( "#nuovo-dettaglio-incasso-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 580,
	height: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {

				if($('#dare_inc_cre').is(':checked')) { var D_A = $("#dare_inc_cre").val(); }
				if($('#avere_inc_cre').is(':checked')) { var D_A = $("#avere_inc_cre").val(); }

				var conto = $("#conti_inc_cre").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
				var idconto = conto.substring(0, 6);
				var importo = $("#importo_inc_cre").val();
				var importoNormalizzato = importo.trim().replace(",", ".");
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var dettagliTable = xmlhttp.responseText;
		        		$("#dettagli_inc_cre").html(dettagliTable);
		        		controllaDettagliRegistrazione("tddettagli_inc_cre","messaggioControlloDettagliIncasso","descreg_inc_cre","descreg_inc_cre_label");
		        	}
			    }
			    xmlhttp.open("GET", "aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
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

$("#modifica-incasso-form").dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-modifica-incasso-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaIncasso").submit();				
			}
		},
		{
			id: "button-dettaglio-modifica-incasso-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-dettaglio-modifica-incasso-form").button("disable");
				$("#importo_detinc_mod").val("");
				$("#nuovo-dettaglio-modifica-incasso-form").dialog( "open" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	document.getElementById("modificaIncasso").reset();
//			        	$("#numfatt_inc_mod").select2("val", "");			        	
		            	$("#tddettagli_inc_mod").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagliIncasso_mod").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaModificaIncassoFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});

//---------------------------------------------------------------------------------		
// MODIFICA INCASSO : controllo campi in pagina
//---------------------------------------------------------------------------------		

function validaModificaIncasso()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_inc_mod", "tddatareg_inc_mod", "messaggioControlloDataIncasso_mod");
	if ($("#messaggioControlloDataIncasso_mod").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_inc_mod").val() != "") {
		if (controllaDescrizione("descreg_inc_mod", "tddescreg_inc_mod", "messaggioControlloDescrizioneIncasso_mod")) 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#causale_inc_mod").val() != "") {
		controllaDettagliRegistrazione("tddettagli_inc_mod","messaggioControlloDettagliIncasso_mod","descreg_inc_mod","descreg_inc_mod_label");
		if ($("#messaggioControlloDettagliIncasso_mod").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if (esito == "111") return true;
	else return false;
}

//---------------------------------------------------------------------------------		

function controllaNegozio_inc_cre(codNegozio) {

	if (codNegozio != "") {
		$("#cliente_inc_cre").val("");
	}
}

//---------------------------------------------------------------------------------		

function controllaNegozio_inc_mod(codNegozio) {

	if (codNegozio != "") {
		$("#cliente_inc_mod").val("");
	}
}

//---------------------------------------------------------------------------------			

$( ".selectmenuCausaleIncCre" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_inc_cre").val();
			
			if (causale != "") {
	        	$( "#tdcausale_inc_cre").removeClass("inputFieldError");	
				$( "#messaggioControlloCausaleIncasso" ).html("");
				
				var xmlhttp = new XMLHttpRequest();
		        xmlhttp.onreadystatechange = function() {
		            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		                $("#conti_inc_cre").html(xmlhttp.responseText);
//		                $("#conti_inc_cre").selectmenu( "refresh" );
		            	$("#button-dettaglio-nuovo-incasso-form").button("enable");	                
		            	validaNuovoIncasso();
		            }
		        }
		        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
		        xmlhttp.send();			
								
			}
			else {
				$("#tdcausale_inc_cre").addClass("inputFieldError");	
				$("#messaggioControlloCausaleIncasso").html("Dato errato");
				$("#button-dettaglio-nuovo-incasso-form").button("disable");	                
			}
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------			

$( ".selectmenuCausaleIncMod" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_inc_mod").val();
			
			if (causale != "") {
	        	$( "#tdcausale_inc_mod").removeClass("inputFieldError");	
				$( "#messaggioControlloCausaleIncasso_mod" ).html("");
				
				var xmlhttp = new XMLHttpRequest();
		        xmlhttp.onreadystatechange = function() {
		            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		                $("#conti_inc_mod").html(xmlhttp.responseText);
//		                $("#conti_inc_mod").selectmenu( "refresh" );
		            	$("#button-dettaglio-modifica-incasso-form").button("enable");	                
		            	validaModificaIncasso();
		            }
		        }
		        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
		        xmlhttp.send();			
								
			}
			else {
				$("#tdcausale_inc_mod").addClass("inputFieldError");	
				$("#messaggioControlloCausaleIncasso_mod").html("Dato errato");
				$("#button-dettaglio-modifica-incasso-form").button("disable");	                
			}
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");
















//$( "#nuovo-dett-modifica-incasso" ).click(function( event ) {
//	$( "#nuovo-dettaglio-modifica-incasso-form" ).dialog( "open" );
//	event.preventDefault();
//});
//
//$( "#nuovo-dettaglio-modifica-incasso-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 550,
//	height: 400,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				
//				// Controllo congruenza conto dettaglio con codice cliente
//				
//				var conto = $("#conti").val();
//				var cliente = $("#cliente").val();
//				
//				var xmlhttp = new XMLHttpRequest();
//			    xmlhttp.onreadystatechange = function() {
//			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//			        	if (xmlhttp.responseText == "Dettaglio ok") {
//			        		
//			        		$("#nuovoDettaglioIncasso").submit();
//				            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
//			        		
//			        	}
//			        	else {
//				            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
//			        	}
//			        }		
//			        
//			    } 
//			    xmlhttp.open("GET", "controlloContoDettaglioIncassoFacade.class.php?modo=start&cliente=" + cliente + "&conto=" + conto, true);
//			    xmlhttp.send();				
//
//				$( this ).dialog( "close" );           				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
////---------------------------------------------------------------------------------				
//function cancellaIncasso(idscad,idinc) {
////---------------------------------------------------------------------------------	
//	$( "#idScadenza" ).val(idscad);
//	$( "#idIncasso" ).val(idinc);
//	$( "#cancella-incasso-form" ).dialog( "open" );
//}
//
//$( "#cancella-incasso-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 300,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//				$("#cancellaIncasso").submit();				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
////---------------------------------------------------------------------------------	
//function cancellaDettaglioIncasso(idconto) {
////---------------------------------------------------------------------------------		
//	$( "#idDettaglioRegistrazione" ).val(idconto);
//	$( "#cancella-dettaglio-modificainc-form" ).dialog( "open" );
//}
//
//$( "#cancella-dettaglio-modificainc-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 300,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//				$("#cancellaDettaglioIncasso").submit();				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});

