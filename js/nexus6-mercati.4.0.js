//---------------------------------------------------------------------------------				
// Mercati
//---------------------------------------------------------------------------------				

//---------------------------------------------------------------------------------		
//Creazione di una nuovo mercato
//---------------------------------------------------------------------------------		
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

//---------------------------------------------------------------------------------		
function modificaMercato(parms) {
//---------------------------------------------------------------------------------			
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

//---------------------------------------------------------------------------------				
function cancellaMercato(idmercato, codmercato) {
//---------------------------------------------------------------------------------				
	$( "#idmercato" ).val(idmercato);
	$( "#codmercatoselezionato" ).val(codmercato);
	$( "#cancella-mercato-form" ).dialog( "open" );
}

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

//---------------------------------------------------------------------------------

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

//---------------------------------------------------------------------------------				
