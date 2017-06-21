//---------------------------------------------------------------------------------				
// Fornitori
//---------------------------------------------------------------------------------				

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

//---------------------------------------------------------------------------------

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

//---------------------------------------------------------------------------------

$(function() {
    $( "#slider-gg-scadenza-fattura_mod" ).slider({
      range: "max",
      min: 0,
      max: 120,
      value: 30,
      step: 10,
      slide: function( event, ui ) {
        $( "#numggscadenzafattura_mod" ).val( ui.value );
      }
    });
    $( "#numggscadenzafattura" ).val( $( "#slider-gg-scadenza-fattura" ).slider( "value" ) );
  });

//---------------------------------------------------------------------------------

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

//---------------------------------------------------------------------------------			
function cancellaFornitore(idfornitore, codfornitore) {
//---------------------------------------------------------------------------------				
	$( "#idfornitore" ).val(idfornitore);
	$( "#cancella-fornitore-form" ).dialog( "open" );
}

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

//---------------------------------------------------------------------------------			
function modificaFornitore(idFornitore) {
//---------------------------------------------------------------------------------		
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");
    		$("#codfornitore_mod").val(datiPagina[0]);
    		$("#desfornitore_mod").val(datiPagina[1]);
    		$("#indfornitore_mod").val(datiPagina[2]);
    		$("#cittafornitore_mod").val(datiPagina[3]);
    		$("#capfornitore_mod").val(datiPagina[4]);

			$("#bonifico_mod").prop("checked", false).button("refresh");
			$("#riba_mod").prop("checked", false).button("refresh");
			$("#rimdiretta_mod").prop("checked", false).button("refresh");
			$("#assegnobancario_mod").prop("checked", false).button("refresh");
			$("#addebitodiretto_mod").prop("checked", false).button("refresh");

    		if (datiPagina[5] == "BONIFICO") {
    			$("#bonifico_mod").prop("checked", true).button("refresh");
    		}
    		else {
        		if (datiPagina[5] == "RIBA") {
        			$("#riba_mod").prop("checked", true).button("refresh");
        		}
        		else {
            		if (datiPagina[5] == "RIM_DIR") {
            			$("#rimdiretta_mod").prop("checked", true).button("refresh");
            		}
            		else {
                		if (datiPagina[5] == "ASS_BAN") {
                			$("#assegnobancario_mod").prop("checked", true).button("refresh");
                		}
                		else {
                    		if (datiPagina[5] == "ADD_DIR") {
                    			$("#addebitodiretto_mod").prop("checked", true).button("refresh");
                    		}
                		}
            		}
        		}
    		}
    		
    		$("#tipoaddebito_mod").val(datiPagina[5]);
    		$("#numggscadenzafattura_mod").val(datiPagina[6]);
    		$( "#slider-gg-scadenza-fattura_mod" ).slider( "value", datiPagina[6] );	
        	
    		$( "#modifica-fornitore-form" ).dialog( "open" );
        }
    }
    xmlhttp.open("GET", "modificaFornitoreFacade.class.php?modo=start&idfornitore=" + idFornitore, true);
    xmlhttp.send();				
}

$( "#modifica-fornitore-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaFornitore").submit();
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


