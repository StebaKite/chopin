//---------------------------------------------------------------------------------				
// Clienti
//---------------------------------------------------------------------------------


$("#nuovoCliente").click(function() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			document.getElementById("nuovoClienteForm").reset();
            if (xmlhttp.responseText != "") {
        		$("#codcli_cre").val(xmlhttp.responseText);
            }			
			$("#nuovo-cliente-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaClienteFacade.class.php?modo=start", true);
	xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-cliente-form").click(
	function() {
		if (validaNuovoCliente()) {
			$("#testo-messaggio-successo").html("Cliente salvato con successo, conto cliente creato!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#nuovoClienteForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il cliente non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------
// CREA CLIENTE : routine di validazione
//---------------------------------------------------------------------------------

function validaNuovoCliente() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codcli_cre").val() != "") {
		if (controllaCodice("codcli_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#descli_cre").val() != "") {
		if (controllaDescrizione("descli_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if (esito == "11") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------------------

function controllaUnivocitaPiva(campo_piva, campo_descli)
{
	var codpiva = $("#" + campo_piva).val();
	var descliente = $("#" + campo_descli).val();

	if (codpiva != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#" + campo_piva + "_messaggio").html(xmlhttp.responseText);
					$("#" + campo_piva + "_control_group").addClass("has-error");
				} else {
					$("#" + campo_piva + "_messaggio").html("");
					$("#" + campo_piva + "_control_group").removeClass("has-error");
				}
	        }
	    } 
	    xmlhttp.open("GET", "cercaPivaClienteFacade.class.php?modo=start&codpiva=" + codpiva + "&descliente=" + descliente, true);
	    xmlhttp.send();		
	}		
}

//---------------------------------------------------------------------------------

function controllaUnivocitaCfis(campo_cfis)
{
	var codfisc = $("#" + campo_cfis).val();

	if (codfisc != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#" + campo_cfis + "_messaggio").html(xmlhttp.responseText);
					$("#" + campo_cfis + "_control_group").addClass("has-error");
				} else {
					$("#" + campo_cfis + "_messaggio").html("");
					$("#" + campo_cfis + "_control_group").removeClass("has-error");
				}
	        }
	    } 
	    xmlhttp.open("GET", "cercaCfisClienteFacade.class.php?modo=start&codfisc=" + codfisc, true);
	    xmlhttp.send();		
	}
}














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

// =============================================================================
// Spostete qui dalla pagina di ricerca cliente
//=============================================================================



function normalizzaCampo(campo) {

	var c = $("#" + campo).val();
	var cNorm = c.trim().replace("&", "e");
	$("#" + campo).val(cNorm);	
}








