//---------------------------------------------------------------------------------				
// Incassi
//---------------------------------------------------------------------------------				

$( "#nuovo-incasso" ).click(function( event ) {
	$("#button-ok-nuovo-incasso-form").button("disable");
	$("#button-dettaglio-nuovo-incasso-form").button("disable");
	$("#descreg_inc_cre").hide();
	$("#descreg_inc_cre_label").hide();

	$("#nuovo-incasso-form").dialog("open");
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
			}
		}
	]
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

				if($('#dare_dett_cre').is(':checked')) { var D_A = $("#dare_dett_cre").val(); }
				if($('#avere_dett_cre').is(':checked')) { var D_A = $("#avere_dett_cre").val(); }

				var conto = $("#conti").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
				var idconto = conto.substring(0, 6);
				var importo = $("#importo_dett_cre").val();
				var importoNormalizzato = importo.trim().replace(",", ".");
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var sottocontiTable = xmlhttp.responseText;
		        		$("#dettagli_cre").html(sottocontiTable);
		        		controllaDettagliRegistrazione("tddettagli_cre");
		        	}
			    }
			    xmlhttp.open("GET", "aggiungiNuovoDettaglioIncassoFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
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
//CREA REGISTRAZIONE : controllo campi in pagina
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
		if (controllaDescrizione("descreg_inc_cre", "tddescreg_inc_cre")) 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#causale_inc_cre").val() != "") {
		controllaDettagliRegistrazione("tddettagli_inc_cre"); 
		if ($("#messaggioControlloDettagli").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
		
	if (esito == "111") {
		$("#button-ok-nuovo-incasso-form").button("enable");
	} else {
		$("#button-ok-nuovo-incasso-form").button("disable");	
	}
}

//---------------------------------------------------------------------------------		

function cercaContiCausale(desconto) {

	var causale = $("#causale_inc_cre").val();

	if ((causale != "") && (desconto != "")) {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	        	$( "#conti_inc_cre" ).html(xmlhttp.responseText);
              $( "#conti_inc_cre" ).selectmenu( "refresh" );	        	
	        }
	    } 
	    xmlhttp.open("GET", "leggiContiCausaleFacade.class.php?modo=start&causale=" + causale + "&desconto=" + desconto, true);
	    xmlhttp.send();		
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
		                $("#conti_inc_cre").selectmenu( "refresh" );
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









//---------------------------------------
//---------------------------------------
//---------------------------------------

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

	var descliente = $("#cliente_inc_cre").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            $( "#select2" ).html(xmlhttp.responseText);
	            $( "#select2" ).selectmenu( "refresh" );
	        }
	    }
	    xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente=" + descliente, true);
	    xmlhttp.send();
	}
})		

//---------------------------------------------------------------------------------	

$( ".selectmenuCausaleIncassi" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_inc_cre").val();
			
			if (causale != "") {
	        	$( "#tdcausale_inc_cre").removeClass("inputFieldError");	
				$( "#messaggioControlloCausaleIncasso" ).html("");
			}
			else {
				$("#messaggioControlloCausaleIncasso").html("Dato errato");
				$("#tdcausale_inc_cre").addClass("inputFieldError");	
			}
		
			var xmlhttp = new XMLHttpRequest();
	        xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	                $( "#conti_inc_cre" ).html(xmlhttp.responseText);
	                $( "#conti_inc_cre" ).selectmenu( "refresh" );
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

