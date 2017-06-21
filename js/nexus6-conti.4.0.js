//---------------------------------------------------------------------------------				
// Conti e Sottoconti
//---------------------------------------------------------------------------------

//---------------------------------------------------------------------------------				
function generaMastrino(codconto, codsottoconto) {
//---------------------------------------------------------------------------------					
	var input_codcontogenera = "<input type='text' id='codcontogenera' name='codcontogenera' value='" + codconto + "' readonly >";
	var input_codsottocontogenera = "<input type='text' id='codsottocontogenera' name='codsottocontogenera' value='" + codsottoconto + "' readonly >";
	
	$( "#id_codcontogenera" ).html(input_codcontogenera);
	$( "#id_codsottocontogenera" ).html(input_codsottocontogenera);
	$( "#generaMastrino-form" ).dialog( "open" );
}

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

//---------------------------------------------------------------------------------		
//Creazione di un nuovo sottoconto in modifica conto
//---------------------------------------------------------------------------------		
$( "#nuovo-sottoconto-modificaconto" ).click(function( event ) {
	$( "#nuovo-sottoconto-modificaconto-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-sottoconto-modificaconto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 650,
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

//---------------------------------------------------------------------------------		
function cancellaConto(codconto) {
//---------------------------------------------------------------------------------			
	$( "#codconto" ).val(codconto);
	$( "#cancella-conto-form" ).dialog( "open" );
}

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

//---------------------------------------------------------------------------------			
function modificaGruppoSottoconto(indgruppo,codconto,codsottoconto) {
//---------------------------------------------------------------------------------					
	$( "#codconto_modgru" ).val(codconto);
	$( "#codsottoconto_modgru" ).val(codsottoconto);
	
	$( "#NS").prop('checked', false).button("refresh");
	$( "#CF").prop('checked', false).button("refresh");
	$( "#CV").prop('checked', false).button("refresh");
	$( "#RC").prop('checked', false).button("refresh");
	
	$( "#" + indgruppo).prop('checked', true).button("refresh");
	$( "#modifica-sottoconto-modificagruppo-form" ).dialog( "open" );
}

$( "#modifica-sottoconto-modificagruppo-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 500,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				var codconto = $("#codconto_modgru").val();
				var codsottoconto = $("#codsottoconto_modgru").val();
				var indgruppo = $("input[name=indgruppo_modgru]:checked").val();				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var sottocontiTable_mod = xmlhttp.responseText;
		        		$("#sottocontiTable_mod").html(sottocontiTable_mod);
		        		var sottocontiTable = sottocontiTable_mod.replace("sottocontiTable_mod","sottocontiTable")
		        		$("#sottocontiTable").html(sottocontiTable);
			        }
			    }
			    xmlhttp.open("GET", "modificaGruppoSottocontoFacade.class.php?modo=start&codconto_modgru=" + codconto + "&codsottoconto_modgru=" + codsottoconto + "&indgruppo_modgru=" + indgruppo, true);
			    xmlhttp.send();				
				
				$(this).dialog('close');
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
// Creazione di un nuovo conto
//---------------------------------------------------------------------------------			
$( "#nuovo-conto" ).click(function( event ) {
	$( "#nuovo-conto-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-conto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 750,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovoConto").submit();				
			}
		},
		{
			text: "Nuovo sottoconto",
			click: function() {
				$( "#nuovo-sottoconto-form" ).dialog( "open" );
				event.preventDefault();
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				$("#annullaNuovoConto").submit();
			}
		}
	]
});

//---------------------------------------------------------------------------------			
function modificaConto(codconto) {
//---------------------------------------------------------------------------------				
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");
    		$("#sottocontiTable_mod").html(datiPagina[0]);
    		$("#codconto_mod").val(datiPagina[1]);
    		$("#desconto_mod").val(datiPagina[2]);    
    		
    		if (datiPagina[3] == "Conto Economico") {
    			$("#contoeco_mod").attr("checked", "checked").button("refresh");
    		}
    		else {
        		if (datiPagina[3] == "Stato Patrimoniale") {
        			$("#contopat_mod").attr("checked", "checked").button("refresh");
        		}    			
    		}
    		
    		if (datiPagina[4] == "Dare") {
    			$("#dare_mod").attr("checked", "checked").button("refresh");
    		}
    		else {
        		if (datiPagina[4] == "Avere") {
        			$("#avere_mod").attr("checked", "checked").button("refresh");
        		}    			
    		}
    		
    		if (datiPagina[5] == "S") {
    			$("#presenzaSi_mod").attr("checked", "checked").button("refresh");
    		}
    		else {
        		if (datiPagina[5] == "N") {
        			$("#presenzaNo_mod").attr("checked", "checked").button("refresh");
        		}    			
    		}
    		
    		if (datiPagina[6] == "S") {
    			$("#sottocontiSi_mod").attr("checked", "checked").button("refresh");
    		}
    		else {
        		if (datiPagina[6] == "N") {
        			$("#sottocontiNo_mod").attr("checked", "checked").button("refresh");
        		}    			
    		}

    		$("#numrigabilancio_mod").val(datiPagina[7]);
    		$("#slider-posizione-bilancio_mod").slider( "value", datiPagina[7] );	
    		
    		$( "#modifica-conto-form" ).dialog( "open" );
        }
    }
    xmlhttp.open("GET", "modificaContoFacade.class.php?modo=start&codconto=" + codconto, true);
    xmlhttp.send();				
}

$( "#modifica-conto-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 750,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaConto").submit();
			}
		},
		{
			text: "Nuovo sottoconto",
			click: function() {
				$( "#nuovo-sottoconto-form" ).dialog( "open" );
				event.preventDefault();
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				$("#annullaModificaConto").submit();
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
		        		$("#sottocontiTable_mod").html(sottocontiTable);
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

//---------------------------------------------------------------------------------				
function cancellaSottoconto(codsottoconto, codconto, funzione) {
//---------------------------------------------------------------------------------	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var sottocontiTable = xmlhttp.responseText;
    		$("#sottocontiTable" + funzione).html(sottocontiTable);
        }
    }
    xmlhttp.open("GET", "togliNuovoSottocontoFacade.class.php?modo=start&codsottoconto_del=" + codsottoconto + "&codconto_del=" + codconto, true);
    xmlhttp.send();				
}

//---------------------------------------------------------------------------------				

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

//---------------------------------------------------------------------------------				

$(function() {
    $( "#slider-posizione-bilancio_mod" ).slider({
      range: "max",
      min: 0,
      max: 100,
      value: 1,
      step: 1,
      slide: function( event, ui ) {
        $( "#numrigabilancio_mod" ).val( ui.value );
      }
    });
    $( "#numrigabilancio_mod" ).val( $( "#slider-posizione-bilancio_mod" ).slider( "value" ) );
  });

//---------------------------------------------------------------------------------				
