//---------------------------------------------------------------------------------				
// Clienti
//---------------------------------------------------------------------------------

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

//---------------------------------------------------------------------------------				
function modificaCliente(idCliente) {
//---------------------------------------------------------------------------------					
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");
    		$("#codcliente_mod").val(datiPagina[0]);
    		$("#descliente_mod").val(datiPagina[1]);
    		$("#indcliente_mod").val(datiPagina[2]);
    		$("#cittacliente_mod").val(datiPagina[3]);
    		$("#capcliente_mod").val(datiPagina[4]);

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
    		$("#codpiva_mod").val(datiPagina[6]);
    		$("#codfisc_mod").val(datiPagina[7]);	
    		$("#catcliente_mod").html(datiPagina[8]);
            $("#catcliente_mod").selectmenu( "refresh" );
        	
    		$( "#modifica-cliente-form" ).dialog( "open" );
        }
    }
    xmlhttp.open("GET", "modificaClienteFacade.class.php?modo=start&idcliente=" + idCliente, true);
    xmlhttp.send();				
}

$( "#modifica-cliente-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 900,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaCliente").submit();
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
function cancellaCliente(idcliente, codcliente) {
//---------------------------------------------------------------------------------					
	$( "#idcliente" ).val(idcliente);
	$( "#cancella-cliente-form" ).dialog( "open" );
}

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


