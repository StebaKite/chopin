$( "#menu-accordion" ).accordion({
	active: false,
	collapsible: true
});

var formatDateJQ="dd/mm/yy";

$( ".button" ).button();

$( ".radioset" ).buttonset();

$( ".tabs" ).tabs({ width: 400 });


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

// Link to open the dialog
$( "#nuovo-dett-modificareg" ).click(function( event ) {
	$( "#nuovo-dettaglio-modificareg-form" ).dialog( "open" );
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

$( ".datepicker" ).datepicker({
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true,
	maxDate: "10y",
	minDate: "-50y"
});

$( ".data" ).datepicker({
	inline: true,
	changeMonth: true,
	changeYear: true,
	bgiframe: true,
	dateFormat: formatDateJQ,
	constrainInput: true
});


$( "#slider" ).slider({
	range: true,
	values: [ 17, 67 ]
});

$( ".spinner" ).spinner();

$( "#menu" ).menu();

$( ".tooltip" ).tooltip();

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

$( ".selectmenuFornitore" )
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

$( ".selectmenuTipoConto" ).selectmenu({ width: 100 });
$( ".selectmenuCategoria" ).selectmenu({ width: 150 });
$( ".selectmenuConto" ).selectmenu({ width: 300 });
$( ".selectmenuCausale" ).selectmenu({ width: 300 });
$( ".selectmenuFornitore" ).selectmenu({ width: 300 });
$( ".selectmenuCliente" ).selectmenu({ width: 300 });

$( "#vtabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
$( "#vtabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

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

function cancellaRegistrazione(idreg) {
	
	$( "#idRegistrazione" ).val(idreg);
	$( "#cancella-registrazione-form" ).dialog( "open" );
}

//---------------------------------------------------------------
//Funzioni per i conti e sottoconti
//---------------------------------------------------------------

function cancellaSottocontoPagina(codsottoconto) {

	$("#" + codsottoconto).remove();	
	
	var rowCount = $("#sottoconti tbody tr").length;
	
	if (rowCount == 0) {
		$( "#sottoconti thead tr" ).remove();		
		$( "#sottoconti" ).removeClass("datiCreateSottile");
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

