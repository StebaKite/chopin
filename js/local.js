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

$( "#nuovo-dettaglio-form" ).dialog({
	autoOpen: false,
	width: 500,
	height: 400,
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
$( "#nuovo-dett" ).click(function( event ) {
	$( "#nuovo-dettaglio-form" ).dialog( "open" );
	event.preventDefault();
});

// Dettaglio Corrispettivi

$( "#nuovo-dettaglio-corrispettivo-form" ).dialog({
	autoOpen: false,
	width: 500,
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


$( "#nuova-data-scadenza-form" ).dialog({
	autoOpen: false,
	width: 500,
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
	width: 500,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
                $("#nuovoDettaglio").submit();				
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
	width: 500,
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
	width: 500,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
             $("#nuovoDettaglioPagamento").submit();				
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
	width: 500,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
           $("#nuovoDettaglioIncasso").submit();				
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

// Nuovo evento
$( "#nuovo-evento-form" ).dialog({
	autoOpen: false,
	width: 500,
	height: 250,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
             $("#nuovoEvento").submit();				
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
$( "#nuovo-evento" ).click(function( event ) {
	$( "#dataevento" ).val("");
	$( "#notaevento" ).val("");	
	$( "#nuovo-evento-form" ).dialog( "open" );
	event.preventDefault();
});

//Modifica registrazione : cancella dettaglio
$( "#cancella-dettaglio-modificareg-form" ).dialog({
	autoOpen: false,
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

//Modifica conto : cancella sottoconto
$( "#cancella-sottoconto-modificaconto-form" ).dialog({
	autoOpen: false,
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
	width: 600,
	height: 200,
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
	width: 400,
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

$( "#nuovo-sottoconto-form" ).dialog({
	autoOpen: false,
	width: 600,
	height: 200,
	buttons: [
		{
			text: "Ok",
			click: function() {
				aggiungiSottoconto();
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
$( "#nuovo-sottoconto" ).click(function( event ) {
	$( "#nuovo-sottoconto-form" ).dialog( "open" );
	event.preventDefault();
});

//Modifica registrazione : cancella scadenza
$( "#cancella-scadenza-modificareg-form" ).dialog({
	autoOpen: false,
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
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true,
	numberOfMonths: 1,
	maxDate: 0
});

$( ".datepicker" ).datepicker({
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true,
	numberOfMonths: 3
});

$( ".data" ).datepicker({
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

$( "#fornitore" ).change(function() {
	
	var desfornitore = $("#fornitore").val();
	var datareg = $("#datareg").val();
	
	if (desfornitore != "") {

		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $( "#datascad" ).val(xmlhttp.responseText);
            }
        }
        xmlhttp.open("GET", "calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore=" + desfornitore + "&datareg=" + datareg, true);
        xmlhttp.send();
	}
});

$( ".scadenzeAperteFornitore" ).change(function() {
	
	var desfornitore = $("#fornitore").val();
	
	if (desfornitore != "") {
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
});

$( ".scadenzeAperteCliente" ).change(function() {

	var descliente = $("#cliente").val();
		
	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $( "#select2" ).html(xmlhttp.responseText);
                $( "#select2" ).selectmenu( "refresh" );
            }
        }
        xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descli=" + descliente, true);
        xmlhttp.send();			
	}		
})

$( "#cliente" ).change(function() {
	
	var cliente = $("#cliente").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $( "#tipoadd" ).val(xmlhttp.responseText);
            }
        }
        xmlhttp.open("GET", "prelevaTipoAddebitoClienteFacade.class.php?modo=start&idcliente=" + cliente, true);
        xmlhttp.send();			
	}
})


$( ".selectmenu" )
	.selectmenu({width: 200})
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

$( ".selectmenuCategoria" )
	.selectmenu({width: 150})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuTipoConto" )
	.selectmenu({width: 100})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuNegozio" )
	.selectmenu({width: 100})
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
$( ".selectmenuCausale" ).selectmenu({ width: 270 });
$( ".selectmenuFornitore" ).selectmenu({ width: 350 });
$( ".selectmenuCliente" ).selectmenu({ width: 350 });
$( ".selectannoesercizio" ).selectmenu({ width: 70 });
$( ".selectmenuDataRipSaldo" ).selectmenu({ width: 120 });
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

$(".numfatt-multiple").select2();
$(".numfatt-cliente-multiple").select2();

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
	
	if (rowCount == 0) {
		$( "#dettagli thead tr" ).remove();		
		$( "#dettagli" ).removeClass("datiCreateSottile");
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
	
	if (rowCount == 0) {
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

//---------------------------------------------------------------
//Funzioni per pagamenti
//---------------------------------------------------------------

//Modifica pagamento : cancella dettaglio
$( "#cancella-dettaglio-modificapag-form" ).dialog({
	autoOpen: false,
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

function cancellaSottocontoPagina(codsottoconto) {

	$("#" + codsottoconto).remove();	
	
	var rowCount = $("#sottoconti tbody tr").length;
	
	if (rowCount == 0) {
		$( "#sottoconti-head thead tr" ).remove();		
		$( "#sottoconti-head" ).removeClass("datiCreateSottile");
	}
	
	var c = parseInt(codsottoconto.toString());
	var index = jQuery.inArray(c,indexSottocontiInseriti);
	if (index == -1) {
		var cc = codsottoconto.toString();
		var index = jQuery.inArray(cc,indexSottocontiInseriti);
	}	
	
	if (index > -1) {
		indexSottocontiInseriti.splice(index, 1);
		aggiornaIndexSottocontiInseriti(indexSottocontiInseriti);

		sottocontiInseriti.splice(index, 1);				
		aggiornaSottocontiInseriti(sottocontiInseriti);
	}
}

function cancellaSottoconto(codsottoconto) {
	
	$( "#codsottoconto" ).val(codsottoconto);
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
	$( "#codfornitoreselezionato" ).val(codfornitore);
	$( "#cancella-fornitore-form" ).dialog( "open" );
}

function cancellaCliente(idcliente, codcliente) {
	
	$( "#idcliente" ).val(idcliente);
	$( "#codclienteselezionato" ).val(codcliente);
	$( "#cancella-cliente-form" ).dialog( "open" );
}

function generaMastrino(codconto, codsottoconto) {
	
	var codSottoconto = pad(codsottoconto, 2);
	if (codSottoconto.substring(0, 1) == "0") {
		var codSottoconto = pad(codsottoconto, 1);
	}
	
	$( "#codcontogenera" ).val(codconto);
	$( "#codsottocontogenera" ).val(codSottoconto);
	$( "#generaMastrino-form" ).dialog( "open" );
}

// ---------------------------------------------------------
// Campi autocomplete
// ---------------------------------------------------------

$( "#fornitore" ).autocomplete({
 	source: elencoFornitori
});	

$( "#cliente" ).autocomplete({
 	source: elencoClienti
});	
