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


$( "#slider" ).slider({
	range: true,
	values: [ 5, 10 ]
});

$( ".spinner" ).spinner();

$( "#menu" ).menu();

$( ".tooltip" ).tooltip();

$( ".selectmenu" )
	.selectmenu({width: 200})
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
	.selectmenu({width: 300})
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

$( ".selectmenuContiInc" )
	.selectmenu({ width: 450 })
	.selectmenu("menuWidget")
	.addClass("overflow");


$( ".selectmenuFornitore" ).selectmenu({ width: 350 });
$( ".selectmenuCliente" ).selectmenu({ width: 350 });
$( ".selectannoesercizio" ).selectmenu({ width: 70 });
$( ".selectmenuDataRipSaldo" ).selectmenu({ width: 150 });

// -----------------------------------------------------------------
// Ajax su campi di input
// -----------------------------------------------------------------

$("#messaggioInfo").animate({opacity: 1.0}, 5000).effect("fade", 3500).fadeOut('slow');
$("#messaggioErrore").animate({opacity: 1.0}, 5000).effect("fade", 6000).fadeOut('slow');


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

function isNumeric(val) {
	var pattern = /^[-+]?(\d+|\d+\.\d*|\d*\.\d+)$/;
	return pattern.test(val);
}

// ---------------------------------------------------------------
// Funzioni per la registrazione e i dettagli
// ---------------------------------------------------------------

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

$( "#fornitore_cre" ).autocomplete({
 	source: elencoFornitori
});	

$( "#cliente_cre" ).autocomplete({
 	source: elencoClienti
});	

$( "#cliente_inc_cre" ).autocomplete({
 	source: elencoClienti
});	

$( "#fornitore_pag_cre" ).autocomplete({
 	source: elencoFornitori
});	

$( "#fornitore_regrap" ).autocomplete({
 	source: elencoFornitori
});	

$( "#cliente_regrap" ).autocomplete({
 	source: elencoClienti
});	
