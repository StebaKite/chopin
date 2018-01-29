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

$(".datepicker").datepicker();

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

$('select').selectpicker();

$('.selectCausale').selectpicker({
	style: 'btn-info',
	size: 'auto',
	width: '300px'
});

$('.selectNegozio').selectpicker({
	style: 'btn-info',
	size: 'auto'
});

$('.selectCliFor').selectpicker({
	style: 'btn-info',
	size: 'auto'
});

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

function pad(num, size)
{
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

function isNumeric(val)
{
	var pattern = /^[-+]?(\d+|\d+\.\d*|\d*\.\d+)$/;
	return pattern.test(val);
}

function escapeRegExp(str) {
	  return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceAll(str, find, replace) {
	return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

function sleep(miliseconds)
{
	var currentTime = new Date().getTime();
	while (currentTime + miliseconds >= new Date().getTime()) { }
}

//---------------------------------------------------------------------------------
//Routine di controllo
//---------------------------------------------------------------------------------

function controllaDataRegistrazione(campoDat)
{
	/**
	 * La data registrazione è obbligatoria Il controllo sulla data
	 * registrazione verificha che la data immessa cada all'interno di uno dei
	 * mesi in linea. I mesi in linea coincidono con le date pianificate di
	 * riporto saldo
	 * 
	 */
	var datareg = $("#" + campoDat).val();

	if (datareg != "") {

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#" + campoDat + "_messaggio").html(xmlhttp.responseText);
					$("#" + campoDat + "_control_group").addClass("has-error");
				} else {
					$("#" + campoDat + "_messaggio").html("");
					$("#" + campoDat + "_control_group").removeClass("has-error");
				}
			}
		}
		xmlhttp.open("GET","controllaDataRegistrazioneFacade.class.php?modo=start&datareg=" + datareg, true);
		xmlhttp.send();
	} else {
		$("#" + campoMsg).html("Dato errato");
		$("#" + campoDatErr).addClass("inputFieldError");
	}
}

//---------------------------------------------------------------------------------

function controllaCodice(campoCod)
{
	if ($("#" + campoCod).val() != "") {
		$("#" + campoCod + "_control_group").removeClass("has-error");
		$("#" + campoCod + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoCod + "_control_group").addClass("has-error");
		$("#" + campoCod + "_messaggio").html("obbligatorio");
		return false;
	}
}

//---------------------------------------------------------------------------------

function controllaDescrizione(campoDes)
{
	if ($("#" + campoDes).val() != "") {
		$("#" + campoDes + "_control_group").removeClass("has-error");
		$("#" + campoDes + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoDes + "_control_group").addClass("has-error");
		$("#" + campoDes + "_messaggio").html("obbligatorio");
		return false;
	}
}

//---------------------------------------------------------------------------------

function controllaCausale(campoCau)
{
	/**
	 * La causale è obbligatoria
	 */
	if ($("#" + campoCau).val() != "") {
		$("#" + campoCau + "_control_group").removeClass("has-error");
		$("#" + campoCau + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoCau + "_control_group").addClass("has-error");
		$("#" + campoCau + "_messaggio").html("obbligatoria");
		return false;
	}
}

//---------------------------------------------------------------------

function controllaClienteFornitore(campoForn, campoCli)
{
	/**
	 * Il cliente e il fornitore sono mutualmente esclusivi Possono mancare
	 * entrambi
	 */
	if (($("#" + campoForn).val() != " ") && ($("#" + campoCli).val() != " "))
	{
		$("#" + campoForn + "_control_group").addClass("has-error");
		$("#" + campoCli + "_control_group").addClass("has-error");
		return false;
	} else if (($("#" + campoForn).val() == " ") && ($("#" + campoCli).val() == " ")) {
		$("#" + campoForn + "_control_group").addClass("has-error");
		$("#" + campoCli + "_control_group").addClass("has-error");
		return false;
	} else {
		$("#" + campoForn + "_control_group").removeClass("has-error");
		$("#" + campoCli + "_control_group").removeClass("has-error");
		return true;
	}
}

//---------------------------------------------------------------------

function controllaNumeroFattura(campoFat)
{
	var numfatt = $("#" + campoFat).val();

	if (numfatt != "") {
		$("#" + campoFat + "_control_group").removeClass("has-error");
		$("#" + campoFat + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoFat + "_control_group").addClass("has-error");
		$("#" + campoFat + "_messaggio").html("obbligatoria");
		return false;
	}
}

//---------------------------------------------------------------------

function controllaNumeroFatturaFornitore(campoForn, campoFat, campoDat)
{
	/**
	 * La fattura del fornitore immessa deve essere univoca
	 */
	var fornitore = $("#" + campoForn).val();
	var numfatt = $("#" + campoFat).val();
	var numfattOrig = $("#" + campoFat + "_orig").val();
	var datareg = $("#" + campoDat).val();

	if ((numfatt != "") && (datareg != "") && (fornitore != "")) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					if (numfatt != numfattOrig) {
						$("#" + campoFat + "_control_group").addClass("has-error");
						$("#" + campoForn + "_control_group").addClass("has-error");
						$("#" + campoFat + "_messaggio").html(xmlhttp.responseText);
					}					
					else {
						$("#" + campoFat + "_control_group").removeClass("has-error");
						$("#" + campoForn + "_control_group").removeClass("has-error");
						$("#" + campoFat + "_control_group").html("");
					}
				}
				else {
					$("#" + campoFat + "_control_group").removeClass("has-error");
					$("#" + campoForn + "_control_group").removeClass("has-error");
					$("#" + campoFat + "_control_group").html("");
				}
			}
		}
		xmlhttp.open("GET","cercaFatturaFornitoreFacade.class.php?modo=start&desfornitore=" + fornitore + "&numfatt=" + numfatt + "&datareg=" + datareg, true);
		xmlhttp.send();
	} 
	else return true;
}

//---------------------------------------------------------------------

function controllaNumeroFatturaCliente(campoCli, campoFat, campoDat)
{
	/**
	 * La fattura del cliente immessa deve essere univoca
	 */
	var cliente = $("#" + campoCli).val();
	var numfatt = $("#" + campoFat).val();
	var datareg = $("#" + campoDat).val();

	if ((numfatt != "") && (datareg != "") && (cliente != "")) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#" + campoFat + "_control_group").addClass("has-error");
					$("#" + campoCli + "_control_group").addClass("has-error");
					$("#" + campoFat + "_control_group").html(xmlhttp.responseText);
				}
				else {
					$("#" + campoFat + "_control_group").removeClass("has-error");
					$("#" + campoCli + "_control_group").removeClass("has-error");
					$("#" + campoFat + "_control_group").html("");
				}
			}
		}
		xmlhttp.open("GET","cercaFatturaClienteFacade.class.php?modo=start&descliente=" + cliente + "&numfatt=" + numfatt + "&datareg=" + datareg, true);
		xmlhttp.send();
	}
	else return true;
}

//---------------------------------------------------------------------

function controllaDettagliRegistrazione(campoDet)
{
	/**
	 * I dettagli della registrazione devono essere presenti Gli importi del
	 * Dare e Avere devono quadrare
	 */
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			if (xmlhttp.responseText != "") {
				$("#" + campoDet + "_control_group").addClass("has-error");
				$("#" + campoDet + "_messaggio").html("Completa i dettagli");
			} else {
				$("#" + campoDet + "_control_group").removeClass("has-error");
				$("#" + campoDet + "_messaggio").html("");
			}
		}
	}
	xmlhttp.open("GET","verificaDettagliRegistrazioneFacade.class.php?modo=start", true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function controllaImporto(campoImp) {

	var importo = $("#" + campoImp).val();

	if (isNumeric(importo)) {
		$("#" + campoImp + "_control_group").removeClass("has-error");
		$("#" + campoImp + "_messaggio").html("");
	} else {
		$("#" + campoImp + "_control_group").addClass("has-error");
		$("#" + campoImp + "_messaggio").html("non valido");
	}
}

//---------------------------------------------------------------------------------

function controllaQuantita(campoQta) {

	var qta = $("#" + campoQta).val();

	if (isNumeric(qta)) {
		$("#" + campoQta + "_control_group").removeClass("has-error");
		$("#" + campoQta + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoQta + "_control_group").addClass("has-error");
		$("#" + campoQta + "_messaggio").html("non valido");
		return false;
	}
}

// ---------------------------------------------------------
// Campi autocomplete
// ---------------------------------------------------------

//$( "#fornitore_cre" ).autocomplete({
// 	source: elencoFornitori
//});	

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
