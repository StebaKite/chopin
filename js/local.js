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
	.selectmenu()
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuFornitore" )
	.selectmenu()
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuCliente" )
	.selectmenu()
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuConto" )
	.selectmenu({width: 200})
	.selectmenu("menuWidget")
	.addClass("overflow");

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

