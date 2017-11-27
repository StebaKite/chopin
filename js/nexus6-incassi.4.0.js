//---------------------------------------------------------------------------------				
// Incassi
//---------------------------------------------------------------------------------				

$( "#nuovo-incasso" ).click(function( event ) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{    	  
			$("#button-ok-nuovo-incasso-form").button("disable");
			$("#button-dettaglio-nuovo-incasso-form").button("disable");
			$("#descreg_inc_cre").hide();
			$("#descreg_inc_cre_label").hide();
			$("#dettagli_inc_cre").hide();

			$('#nuovoIncasso').trigger("reset");
			$('#scadenze_chiuse_inc_cre').html("");
			$('#scadenze_aperte_inc_cre').html("");
			
			$("#nuovo-incasso-form").dialog("open");			
		}
	} 
	xmlhttp.open("GET", "creaIncassoFacade.class.php?modo=start", true);
	xmlhttp.send();		
});

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
// CREA NUOVO INCASSO : controllo campi in pagina
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
	
	if (esito == "111") {
		$("#button-ok-nuovo-incasso-form").button("enable");
	} else {
		$("#button-ok-nuovo-incasso-form").button("disable");	
	}
}

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
	
	if (esito == "111") {
		$("#button-ok-modifica-incasso-form").button("enable");
	} else {
		$("#button-ok-modifica-incasso-form").button("disable");	
	}
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

//---------------------------------------------------------------------------------	

$( ".scadenzeAperteCliente" ).keyup(function() {

	var descliente = $("#cliente_inc_cre").val();
	if($('#villa_inc_cre').is(':checked')) var codnegozio = $("#villa_inc_cre").val();
	if($('#brembate_inc_cre').is(':checked')) var codnegozio = $("#brembate_inc_cre").val();
	if($('#trezzo_inc_cre').is(':checked')) var codnegozio = $("#trezzo_inc_cre").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				var response = xmlhttp.responseText;
				var response = response.replace(/\t+/g, "");
				var datiPagina = response.split("|");
				var numfatt_daincassare_cre = datiPagina[0];
				var numfatt_incassate_cre = datiPagina[1];

				// Viene utilizzato solo dalla creazione incasso perchè la modifica ha il 
				// campo fornitore in pagina settato a readonly
				
				$("#scadenze_chiuse_inc_cre").html(numfatt_incassate_cre);
				$("#scadenze_aperte_inc_cre").html(numfatt_daincassare_cre);
	        }
	    }
	    xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente_inc_cre=" + descliente + "&codnegozio_inc_cre=" + codnegozio, true);
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
			$("#" + idTableChiuse).html("");
			$("#" + idTableAperte).html("");
			
        	var response = xmlhttp.responseText;
			var datiPagina = response.split("|");
			
			$("#" + idTableChiuse).html(datiPagina[0]);
			$("#" + idTableAperte).html(datiPagina[1]);
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
			$("#" + idTableChiuse).html("");
			$("#" + idTableAperte).html("");

			var response = xmlhttp.responseText;
			var datiPagina = response.split("|");
			
			$("#" + idTableChiuse).html(datiPagina[0]);
			$("#" + idTableAperte).html(datiPagina[1]);
		}
	}
	xmlhttp.open("GET", "rimuoviFatturaIncassateFacade.class.php?modo=start&idscadcli=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
	xmlhttp.send();		
}

//---------------------------------------------------------------------------------		

function modificaIncasso(idIncasso)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {    	  
			var response = xmlhttp.responseText;
			var response = response.replace(/\t+/g, "");
			var datiPagina = response.split("|");
			var datareg_mod = datiPagina[0];
			var descreg_mod = datiPagina[1];
			var causale_mod = datiPagina[2];
			var codneg_mod  = datiPagina[3];
			var cliente_mod = datiPagina[4];
			var numfatt_daincassare_mod = datiPagina[5];
			var numfatt_incassate_mod = datiPagina[6];
			var dettagli_mod = datiPagina[7];
			var conti = datiPagina[8];
			
			$("#datareg_inc_mod").val(datareg_mod);
			$("#descreg_inc_mod").val(descreg_mod);
			$("#causale_inc_mod").val(causale_mod);
			$("#causale_inc_mod").selectmenu( "refresh" );
			
			if (codneg_mod == "VIL") {
				$("#villa_inc_mod").prop("checked", true).button("refresh");
	  			$("#brembate_inc_mod").prop("checked", false).button("refresh");
	  			$("#trezzo_inc_mod").prop("checked", false).button("refresh");
			}
			else {
	    		if (codneg_mod == "BRE") {
					$("#villa_inc_mod").prop("checked", false).button("refresh");
	    			$("#brembate_inc_mod").prop("checked", true).button("refresh");
	    			$("#trezzo_inc_mod").prop("checked", false).button("refresh");
	    		}
	    		else {
	        		if (codneg_mod == "TRE") {
						$("#villa_inc_mod").prop("checked", false).button("refresh");
		    			$("#brembate_inc_mod").prop("checked", false).button("refresh");
	        			$("#trezzo_inc_mod").prop("checked", true).button("refresh");
	        		}
	    		}
			}        	

			$("#cliente_inc_mod").val(cliente_mod);
			
			$("#scadenze_chiuse_inc_mod").html(numfatt_incassate_mod);
			$("#scadenze_aperte_inc_mod").html(numfatt_daincassare_mod);
			
			$("#dettagli_inc_mod").html(dettagli_mod);
			$("#conti_inc_mod").html(conti);
			$("#conti_inc_mod").selectmenu( "refresh" );
			
			$( "#modifica-incasso-form" ).dialog( "open" );

			validaModificaIncasso();
		}
	} 
	xmlhttp.open("GET", "modificaIncassoFacade.class.php?modo=start&idinc=" + idIncasso, true);
	xmlhttp.send();		
}
















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

