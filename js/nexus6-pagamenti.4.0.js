//---------------------------------------------------------------------------------				
// Pagamenti
//---------------------------------------------------------------------------------				

$( "#nuovo-pagamento" ).click(function( event ) {
	$("#button-ok-nuovo-pagamento-form").button("disable");
	$("#button-dettaglio-nuovo-pagamento-form").button("disable");
	$("#descreg_pag_cre").hide();
	$("#descreg_pag_cre_label").hide();
	$("#dettagli_pag_cre").hide();

	$("#nuovo-pagamento-form").dialog("open");
});

$("#nuovo-pagamento-form").dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-nuovo-pagamento-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoPagamento").submit();				
			}
		},
		{
			id: "button-dettaglio-nuovo-pagamento-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-dettaglio-nuovo-pagamento-form").button("disable");
				$("#importo_detpag_cre").val("");
				$("#nuovo-dettaglio-pagamento-form").dialog( "open" );
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
			        	$("#numfatt_pag_cre").select2("val", "");			        	
		            	$("#tddettagli_pag_cre").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagliPagamento").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaNuovoPagamentoFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});

$( "#nuovo-dettaglio-pagamento-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 580,
	height: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {

				if($('#dare_pag_cre').is(':checked')) { var D_A = $("#dare_pag_cre").val(); }
				if($('#avere_pag_cre').is(':checked')) { var D_A = $("#avere_pag_cre").val(); }

				var conto = $("#conti_pag_cre").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
				var idconto = conto.substring(0, 6);
				var importo = $("#importo_pag_cre").val();
				var importoNormalizzato = importo.trim().replace(",", ".");
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var dettagliTable = xmlhttp.responseText;
		        		$("#dettagli_pag_cre").html(dettagliTable);
		        		$("#dettagli_pag_cre").show();
		        		controllaDettagliRegistrazione("tddettagli_pag_cre","messaggioControlloDettagliPagamento","descreg_pag_cre","descreg_pag_cre_label");
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
// CREA NUOVO PAGAMENTO : controllo campi in pagina
//---------------------------------------------------------------------------------		

function validaNuovoPagamento()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_pag_cre", "tddatareg_pag_cre", "messaggioControlloDataPagamento");
	if ($("#messaggioControlloDataPagamento").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_pag_cre").val() != "") {
		if (controllaDescrizione("descreg_pag_cre", "tddescreg_pag_cre", "messaggioControlloDescrizionePagamento")) 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#causale_pag_cre").val() != "") {
		controllaDettagliRegistrazione("tddettagli_pag_cre","messaggioControlloDettagliPagamento","descreg_pag_cre","descreg_pag_cre_label");
		if ($("#messaggioControlloDettagliPagamento").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if (esito == "111") {
		$("#button-ok-nuovo-pagamento-form").button("enable");
	} else {
		$("#button-ok-nuovo-pagamento-form").button("disable");	
	}
}

//---------------------------------------------------------------------------------		

function controllaNegozio_pag_cre(codNegozio) {

	if (codNegozio != "") {
		$("#fornitore_pag_cre").val("");
	}
}

//---------------------------------------------------------------------------------			

$( ".selectmenuCausalePagCre" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_pag_cre").val();
			
			if (causale != "") {
	        	$( "#tdcausale_pag_cre").removeClass("inputFieldError");	
				$( "#messaggioControlloCausalePagamento" ).html("");
				
				var xmlhttp = new XMLHttpRequest();
		        xmlhttp.onreadystatechange = function() {
		            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		                $("#conti_pag_cre").html(xmlhttp.responseText);
//		                $("#conti_pag_cre").selectmenu( "refresh" );
		            	$("#button-dettaglio-nuovo-pagamento-form").button("enable");	                
		            	validaNuovoPagamento();
		            }
		        }
		        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
		        xmlhttp.send();			
								
			}
			else {
				$("#tdcausale_pag_cre").addClass("inputFieldError");	
				$("#messaggioControlloCausalePagamento").html("Dato errato");
				$("#button-dettaglio-nuovo-pagamento-form").button("disable");	                
			}
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------	

$( ".scadenzeAperteFornitore" ).keyup(function() {

	var desfornitore = $("#fornitore_pag_cre").val();
	if($('#villa_pag_cre').is(':checked')) var codnegozio = $("#villa_pag_cre").val();
	if($('#brembate_pag_cre').is(':checked')) var codnegozio = $("#brembate_pag_cre").val();
	if($('#trezzo_pag_cre').is(':checked')) var codnegozio = $("#trezzo_pag_cre").val();

	if (desfornitore != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            $( "#numfatt_pag_cre" ).html(xmlhttp.responseText);
	        }
	    }
	    xmlhttp.open("GET", "ricercaScadenzeAperteFornitoreFacade.class.php?modo=start&desfornitore_pag_cre=" + desfornitore + "&codnegozio_pag_cre=" + codnegozio, true);
	    xmlhttp.send();
	}
})		

//---------------------------------------------------------------------------------	

$(".numfatt-fornitore-multiple").select2().on("change", function() {
	var numfatt = $("#numfatt_pag_cre").val();
	if (numfatt == undefined) {
		$("#messaggioControlloNumeroFatturaPagamento").html("Dato errato");
		$("#tdnumfatt_pag_cre").addClass("inputFieldError");			
	}
	else {
		if (numfatt.length > 0) {
	    	$( "#tdnumfatt_pag_cre").removeClass("inputFieldError");	
			$( "#messaggioControlloNumeroFatturaPagamento" ).html("");
		}
		else {
			$("#messaggioControlloNumeroFatturaPagamento").html("Dato errato");
			$("#tdnumfatt_pag_cre").addClass("inputFieldError");	
		}		
	}
})	



//---------------------------------------
//---------------------------------------
//---------------------------------------






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
