//---------------------------------------------------------------------------------				
// Progressivi
//---------------------------------------------------------------------------------				

//---------------------------------------------------------------------------------				
function modificaProgressivoFattura(catCliente, codNegozio) {
//---------------------------------------------------------------------------------				
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");
        	
    		$("#catcliente").val(datiPagina[0]);
    		$("#codnegozio").val(datiPagina[1]);
    		$("#numfatt").val(datiPagina[2]);    
    		$("#notatesta").val(datiPagina[3]);    
    		$("#notapiede").val(datiPagina[4]);    
    		
    		$( "#modifica-progressivo-form" ).dialog( "open" );
        }
    }
    xmlhttp.open("GET", "modificaProgressivoFatturaFacade.class.php?modo=start&catcliente=" + catCliente + "&codnegozio=" + codNegozio, true);
    xmlhttp.send();				
}

$( "#modifica-progressivo-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 900,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaProgressivo").submit();
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

