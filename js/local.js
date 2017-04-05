$( "#menu-accordion" ).accordion({
	active: false,
	collapsible: true
});

var formatDateJQ="dd/mm/yy";

$( ".button" ).button();

$( ".radioset" ).buttonset();

$( ".tabs" ).tabs();

$( "#vtabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
$( "#vtabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

$( "#bonifico" ).click(function( event ) {
	$( "#slider-gg-scadenza-fattura" ).show();
    $( "#numggscadenzafattura" ).val( $( "#slider-gg-scadenza-fattura" ).slider( "value" ) );	
});

$( "#riba" ).click(function( event ) {
	$( "#slider-gg-scadenza-fattura" ).show();
    $( "#numggscadenzafattura" ).val( $( "#slider-gg-scadenza-fattura" ).slider( "value" ) );	
});

$( "#rimdiretta" ).click(function( event ) {
	$( "#slider-gg-scadenza-fattura" ).show();
    $( "#numggscadenzafattura" ).val( $( "#slider-gg-scadenza-fattura" ).slider( "value" ) );	
});

$( "#assegnobancario" ).click(function( event ) {
	$( "#slider-gg-scadenza-fattura" ).show();
    $( "#numggscadenzafattura" ).val( $( "#slider-gg-scadenza-fattura" ).slider( "value" ) );	
});

$( "#addebitodiretto" ).click(function( event ) {
	$( "#slider-gg-scadenza-fattura" ).hide();
    $( "#numggscadenzafattura" ).val( 0 );
});


$( "#msg" ).dialog({
	autoOpen: false,
	modal: true,
	width: 500,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiDettaglio();
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


$( "#nuovo-mercato" ).click(function( event ) {
	$( "#nuovo-mercato-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-mercato-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	height: 280,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$( this ).dialog( "close" );
                $("#nuovoMercato").submit();				

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


$( "#modifica-mercato-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	height: 280,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$( this ).dialog( "close" );
                $("#modificaMercato").submit();				

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

//Dettaglio Registrazione

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

// Link to open the dialog
$( "#nuovo-dett" ).click(function( event ) {
	$( "#nuovo-dettaglio-form" ).dialog( "open" );
	event.preventDefault();
});

// Dettaglio Corrispettivi

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

// Link to open the dialog
$( "#nuovo-dett-corrisp" ).click(function( event ) {
	$( "#nuovo-dettaglio-corrispettivo-form" ).dialog( "open" );
	event.preventDefault();
});

//----------------------------------------------

//Dettaglio Pagamento

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

//Link to open the dialog
$( "#nuovo-dett-pagam" ).click(function( event ) {
	$( "#nuovo-dettaglio-pagamento-form" ).dialog( "open" );
	event.preventDefault();
});

//----------------------------------------------

//Dettaglio Incasso

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

//Link to open the dialog
$( "#nuovo-dett-incasso" ).click(function( event ) {
	$( "#nuovo-dettaglio-incasso-form" ).dialog( "open" );
	event.preventDefault();
});

//--------------------------------------------------------

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

// Link to open the dialog
$( "#nuova-data-scad" ).click(function( event ) {
	$( "#datascad" ).val("");	
	$( "#nuova-data-scadenza-form" ).dialog( "open" );
	event.preventDefault();
});

// Modifica registrazione : aggiunta nuovo dettaglio
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

//Link to open the dialog
$( "#nuovo-dett-modificareg" ).click(function( event ) {
	$( "#nuovo-dettaglio-modificareg-form" ).dialog( "open" );
	event.preventDefault();
});


//Modifica registrazione : aggiunta nuovo dettaglio
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

//Link to open the dialog
$( "#nuova-scad-modificareg" ).click(function( event ) {
	$( "#nuova-scadenza-modificareg-form" ).dialog( "open" );
	event.preventDefault();
});



//Modifica registrazione : aggiunta nuovo dettaglio
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

//Link to open the dialog
$( "#nuovo-dett-modifica-pagamento" ).click(function( event ) {
	$( "#nuovo-dettaglio-modifica-pagamento-form" ).dialog( "open" );
	event.preventDefault();
});

//Modifica registrazione : aggiunta nuovo dettaglio
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

//Link to open the dialog
$( "#nuovo-dett-modifica-incasso" ).click(function( event ) {
	$( "#nuovo-dettaglio-modifica-incasso-form" ).dialog( "open" );
	event.preventDefault();
});

//Modifica registrazione : cancella dettaglio
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

//Cancella conto
$( "#cancella-conto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
         $("#cancellaConto").submit();				
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

//Cancella causale
$( "#cancella-causale-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
       $("#cancellaCausale").submit();				
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

//Cancella fornitore
$( "#cancella-fornitore-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#cancellaFornitore").submit();				
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

//Cancella cliente
$( "#cancella-cliente-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#cancellaCliente").submit();				
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

//Cancella cliente
$( "#cancella-mercato-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#cancellaMercato").submit();				
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

//Modifica conto : modifica gruppo sottoconto
$( "#modifica-sottoconto-modificagruppo-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 500,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
         $("#modificaGruppoSottoconto").submit();				
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

//Modifica conto : cancella sottoconto
$( "#cancella-sottoconto-modificaconto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
           $("#cancellaSottoconto").submit();				
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

//Modifica conto : aggiunta nuovo sottoconto
$( "#nuovo-sottoconto-modificaconto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	height: 250,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
             $("#nuovoSottoconto").submit();				
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

// Genera mastrino fornitore
$( "#generaMastrino-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 450,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#generaMastrino").submit();				
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

//Link to open the dialog
$( "#nuovo-sottoconto-modificaconto" ).click(function( event ) {
	$( "#nuovo-sottoconto-modificaconto-form" ).dialog( "open" );
	event.preventDefault();
});

//Cancella registrazione
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

//Cancella corrispettivo
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

//Cancella pagamento
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

//Cancella incasso
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

$( "#nuovo-sottoconto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 600,
	height: 200,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				var codsottoconto = $("#codsottoconto").val();
				var dessottoconto = $("#dessottoconto").val();
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var sottocontiTable = xmlhttp.responseText;
		        		$("#sottocontiTable").html(sottocontiTable);
			        }
			    }
			    xmlhttp.open("GET", "aggiungiNuovoSottocontoFacade.class.php?modo=start&codsottoconto=" + codsottoconto + "&dessottoconto=" + dessottoconto, true);
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

$( "#nuovo-sottoconto" ).click(function( event ) {
	$( "#nuovo-sottoconto-form" ).dialog( "open" );
	event.preventDefault();
});

//Modifica registrazione : cancella scadenza
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

$( "#nuovo-dettaglio-fattura-aziende-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 510,
	height: 420,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiDettaglio();
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

// Link to open the dialog
$( "#nuovo-dett-fattura-aziende" ).click(function( event ) {
	$( "#nuovo-dettaglio-fattura-aziende-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-fattura-cliente-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 510,
	height: 420,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiDettaglio();
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

//Link to open the dialog
$( "#nuovo-dett-fattura-cliente" ).click(function( event ) {
	$( "#nuovo-dettaglio-fattura-cliente-form" ).dialog( "open" );
	event.preventDefault();
});


$( "#nuova-causale" ).click(function( event ) {
	$( "#nuova-causale-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuova-causale-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 750,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovaCausale").submit();				
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

$( "#nuovo-fornitore" ).click(function( event ) {
	$( "#nuovo-fornitore-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-fornitore-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoFornitore").submit();				
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

$( "#nuovo-cliente" ).click(function( event ) {
	$( "#nuovo-cliente-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-cliente-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 900,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoCliente").submit();				
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

// ----------------------------------------------------
// Link per la sottomissione del form elenco eventi

$( "#aperti" ).click(function( event ) {
    $("#elencoEventi").submit();				
	event.preventDefault();
});

$( "#chiusi" ).click(function( event ) {
	$("#elencoEventi").submit();				
	event.preventDefault();
});

$( "#tutti" ).click(function( event ) {
	$("#elencoEventi").submit();				
	event.preventDefault();
});
//----------------------------------------------------


$( ".dataregpicker" ).datepicker({
	showAnim: "slideDown",
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true,
	numberOfMonths: 1,
	maxDate: 0
});

$( ".datepicker" ).datepicker({
	showAnim: "slideDown",
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true,
	numberOfMonths: 3
});

$( ".data" ).datepicker({
	showAnim: "slideDown",
	inline: true,
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true
});

$(function() {
    $( "#slider-gg-scadenza-fattura" ).slider({
      range: "max",
      min: 0,
      max: 120,
      value: 30,
      step: 10,
      slide: function( event, ui ) {
        $( "#numggscadenzafattura" ).val( ui.value );
      }
    });
    $( "#numggscadenzafattura" ).val( $( "#slider-gg-scadenza-fattura" ).slider( "value" ) );
  });

$(function() {
    $( "#slider-posizione-bilancio" ).slider({
      range: "max",
      min: 0,
      max: 100,
      value: 1,
      step: 1,
      slide: function( event, ui ) {
        $( "#numrigabilancio" ).val( ui.value );
      }
    });
    $( "#numrigabilancio" ).val( $( "#slider-posizione-bilancio" ).slider( "value" ) );
  });


$( "#slider" ).slider({
	range: true,
	values: [ 5, 10 ]
});

$( ".spinner" ).spinner();

$( "#menu" ).menu();

$( ".tooltip" ).tooltip();

// -------------------------------------------------
// Ajax su select menu
// -------------------------------------------------

$( ".selectmenuCausale" )
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
	.selectmenu({width: 150})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuCausaleRapido" )
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
	            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {}
	        }
	        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
	        xmlhttp.send();			
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

// -------------------------------------------

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

//---------------------------------------------------------------------

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

//---------------------------------------------------------------------

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

/* *********************************************************************************************
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


$('input[type=radio][name=codneg]').change(function() {
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $( "#mercati" ).html(xmlhttp.responseText);
            $( "#mercati" ).selectmenu( "refresh" );
        }
    }
    xmlhttp.open("GET", "leggiMercatiNegozioFacade.class.php?modo=start&negozio=" + this.value, true);
    xmlhttp.send();			
});


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


$( ".selectmenu" )
	.selectmenu({width: 200})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuCausale" )
	.selectmenu({width: 350})
	.selectmenu("menuWidget")
	.addClass("overflow");

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

$( ".selectmenuMeserif" )
	.selectmenu({width: 100})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuCliente" )
	.selectmenu({width: 200})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuConto" )
	.selectmenu({width: 200})
	.selectmenu("menuWidget")
	.addClass("overflow");



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

$( ".selectmenuContoCambioConto" )
	.selectmenu({width: 400})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuCategoria" )
	.selectmenu({width: 150})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuTipoConto" )
	.selectmenu({width: 100})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuNegozio" )
	.selectmenu({width: 120})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuStato" )
	.selectmenu({width: 100})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectCategoriaConti" )
	.selectmenu({width: 150})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuConti" )
	.selectmenu({ width: 500 })
	.selectmenu("menuWidget")
	.addClass("overflow");


$( ".selectmenuTipoConto" ).selectmenu({ width: 100 });
$( ".selectmenuCategoria" ).selectmenu({ width: 150 });
$( ".selectmenuConto" ).selectmenu({ width: 300 });
$( ".selectmenuContoCambioConto" ).selectmenu({ width: 350 });
$( ".selectmenuFornitore" ).selectmenu({ width: 350 });
$( ".selectmenuCliente" ).selectmenu({ width: 350 });
$( ".selectannoesercizio" ).selectmenu({ width: 70 });
$( ".selectmenuDataRipSaldo" ).selectmenu({ width: 150 });
$( ".selectmenuCategoria" ).selectmenu({ width: 250 });
$( ".selectmenuCliente" ).selectmenu({ width: 350 });

// -----------------------------------------------------------------
// Ajax su campi di input
// -----------------------------------------------------------------

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

$("#messaggioInfo").animate({opacity: 1.0}, 5000).effect("fade", 3500).fadeOut('slow');
$("#messaggioErrore").animate({opacity: 1.0}, 5000).effect("fade", 6000).fadeOut('slow');

$(function() {
	$('tr.parent') 
		.css("cursor","pointer") 
		.attr("title","Click per espandere/collassare") 
		.click(function(){
			$(this).siblings('.child-'+this.id).toggle();
		});
	
	$('tr.parentAperto') 
		.css("cursor","pointer") 
		.attr("title","Click per espandere/collassare") 
		.click(function(){
		$(this).siblings('.child-'+this.id).toggle();
	});
	
	$('tr.parentErrato') 
		.css("cursor","pointer") 
		.attr("title","Click per espandere/collassare") 
		.click(function(){
		$(this).siblings('.child-'+this.id).toggle();
	});
	
	$('tr[@class^=child-]').hide().children('td');

});

$(".numfatt-multiple").select2()
.on("change", function() {
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
	
$(".numfatt-cliente-multiple").select2()
.on("change", function() {
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

// Hover states on the static widgets
$( "#dialog-link, #icons li" ).hover(
	function() {
		$( this ).addClass( "ui-state-hover" );
	},
	function() {
		$( this ).removeClass( "ui-state-hover" );
	}
);

//---------------------------------------------------------------
// Funzioni comuni
//---------------------------------------------------------------

function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
// ---------------------------------------------------------------
// Funzioni per la registrazione e i dettagli
// ---------------------------------------------------------------

function cancellaDettaglio(idconto) {
	
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificareg-form" ).dialog( "open" );
}

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

function cancellaDettaglioFattura(id) {
	
	$("#" + id).remove();	
	
 	var rowCount = $("#dettagli tbody tr").length;
	
	if (rowCount == 1) {
		$( "#dettagli thead tr" ).remove();		
		$( "#dettagli" ).removeClass("datiCreateSottile");
	}
	
	var c = parseInt(id.toString());
	var index = jQuery.inArray(c,indexDettInseriti);
	if (index == -1) {
		var cc = id.toString();
		var index = jQuery.inArray(cc,indexDettInseriti);
	}	
	
	if (index > -1) {
 		indexDettInseriti.splice(index, 1);
 		aggiornaIndexDettaglioInseriti(indexDettInseriti);

 		dettInseriti.splice(index, 1);				
 		aggiornaDettaglioInseriti(dettInseriti);
	}

	// Scopro il bottone nuovo dettaglio nascosto dalla funzione di creazione fattura nel caso di contributo

	$("#nuovo-dett-fattura-cliente").show();
	$("#nuovo-dett-fattura-aziende").show();
}

function cancellaScadenza(idscadenza) {
	
	$( "#idScadenzaRegistrazione" ).val(idscadenza);
	$( "#cancella-scadenza-modificareg-form" ).dialog( "open" );
}

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

function cancellaRegistrazione(idreg) {
	
	$( "#idRegistrazione" ).val(idreg);
	$( "#cancella-registrazione-form" ).dialog( "open" );
}

function cancellaCorrispettivo(idreg) {
	
	$( "#idRegistrazione" ).val(idreg);
	$( "#cancella-corrispettivo-form" ).dialog( "open" );
}

function cancellaPagamento(idscad,idpag) {
	
	$( "#idScadenza" ).val(idscad);
	$( "#idPagamento" ).val(idpag);
	$( "#cancella-pagamento-form" ).dialog( "open" );
}

function cancellaIncasso(idscad,idinc) {
	
	$( "#idScadenza" ).val(idscad);
	$( "#idIncasso" ).val(idinc);
	$( "#cancella-incasso-form" ).dialog( "open" );
}

//---------------------------------------------------------------
//Funzioni per pagamenti
//---------------------------------------------------------------

//Modifica pagamento : cancella dettaglio
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

function cancellaDettaglioPagamento(idconto) {
	
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificapag-form" ).dialog( "open" );
}

//---------------------------------------------------------------
// Funzioni per incassi
//---------------------------------------------------------------

//Modifica incasso : cancella dettaglio
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

function cancellaDettaglioIncasso(idconto) {
	
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificainc-form" ).dialog( "open" );
}

//---------------------------------------------------------------
//Funzioni per i conti e sottoconti
//---------------------------------------------------------------

function cancellaSottocontoPagina(codsottoconto, dessottoconto) {
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var sottocontiTable = xmlhttp.responseText;
    		$("#sottocontiTable").html(sottocontiTable);
        }
    }
    xmlhttp.open("GET", "togliNuovoSottocontoFacade.class.php?modo=start&codsottoconto=" + codsottoconto + "&dessottoconto=" + dessottoconto, true);
    xmlhttp.send();				
}

function modificaGruppoSottoconto(indgruppo,codsottoconto) {
		
	$( "#codsottoconto" ).val(codsottoconto);
	$( "#" + indgruppo).prop('checked', true).button("refresh");
	$( "#modifica-sottoconto-modificagruppo-form" ).dialog( "open" );
}

function cancellaSottoconto(codsottoconto,codconto) {
	
	$( "#codsottoconto_del" ).val(codsottoconto);
	$( "#codconto_del" ).val(codconto);
	$( "#cancella-sottoconto-modificaconto-form" ).dialog( "open" );
}

function cancellaConto(codconto) {
	
	$( "#codconto" ).val(codconto);
	$( "#cancella-conto-form" ).dialog( "open" );
}

function cancellaCausale(codcausale) {
	
	$( "#codcausale" ).val(codcausale);
	$( "#cancella-causale-form" ).dialog( "open" );
}

function cancellaFornitore(idfornitore, codfornitore) {
	
	$( "#idfornitore" ).val(idfornitore);
	$( "#cancella-fornitore-form" ).dialog( "open" );
}

function cancellaCliente(idcliente, codcliente) {
	
	$( "#idcliente" ).val(idcliente);
	$( "#cancella-cliente-form" ).dialog( "open" );
}

function cancellaMercato(idmercato, codmercato) {
	
	$( "#idmercato" ).val(idmercato);
	$( "#codmercatoselezionato" ).val(codmercato);
	$( "#cancella-mercato-form" ).dialog( "open" );
}

function modificaMercato(parms) {
	
	var parm = parms.split("#");
	
	$( "#idmercato_mod" ).val(parm[0]);
	$( "#codmercato_mod" ).val(parm[1]);
	$( "#desmercato_mod" ).val(parm[2].replace("@","'"));
	$( "#cittamercato_mod" ).val(parm[3].replace("@","'"));

	if (parm[4] == "VIL") $( "#villamod" ).attr("checked", "checked").button("refresh");
	if (parm[4] == "BRE") $( "#brembatemod" ).attr("checked", "checked").button("refresh");
	if (parm[4] == "TRE") $( "#trezzomod" ).attr("checked", "checked").button("refresh");
	
	$( "#modifica-mercato-form" ).dialog( "open" );	
}

function generaMastrino(codconto, codsottoconto) {
		
	var input_codcontogenera = "<input type='text' id='codcontogenera' name='codcontogenera' value='" + codconto + "' readonly >";
	var input_codsottocontogenera = "<input type='text' id='codsottocontogenera' name='codsottocontogenera' value='" + codsottoconto + "' readonly >";
	
	$( "#id_codcontogenera" ).html(input_codcontogenera);
	$( "#id_codsottocontogenera" ).html(input_codsottocontogenera);
	$( "#generaMastrino-form" ).dialog( "open" );
}


function escapeRegExp(str) {
	  return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}
function replaceAll(str, find, replace) {
  return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

//---------------------------------------------------------
//Menu di navigazione
//---------------------------------------------------------

$('.nav li').hover(
	function () {
		$('ul', this).fadeIn();
	},
	function () {
		$('ul', this).fadeOut();
	}
);

// ---------------------------------------------------------
// Campi autocomplete
// ---------------------------------------------------------

$( "#fornitore" ).autocomplete({
 	source: elencoFornitori
});	

$( "#cliente" ).autocomplete({
 	source: elencoClienti
});	

$( "#fornitore_regrap" ).autocomplete({
 	source: elencoFornitori
});	

$( "#cliente_regrap" ).autocomplete({
 	source: elencoClienti
});	
