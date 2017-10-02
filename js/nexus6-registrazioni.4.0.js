//---------------------------------------------------------------------------------				
// Registrazioni
//---------------------------------------------------------------------------------				

//---------------------------------------------------------------------------------		
// Creazione di una nuova registrazione
//---------------------------------------------------------------------------------		
$( "#nuova-registrazione" ).click(function( event ) {
	$("#button-ok-nuova-registrazione-form").button("disable");
	$("#button-dettaglio-nuova-registrazione-form").button("disable");
	$("#button-dettaglio-nuova-scadenza-form").button("disable");
	
	$("#descreg_cre").hide();
	$("#descreg_cre_label").hide();
	$("#scadenzesuppl_cre").hide();
	$("#datascad_cre_label").hide();
	$("#numfatt_cre_label").hide();
	$("#numfatt_cre").hide();
	$("#dettagli_cre").hide();
	$("#dettagli_inc_cre").hide();

	$("#nuova-registrazione-form").dialog("open");
});

$( "#nuova-registrazione-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-nuova-registrazione-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovaRegistrazione").submit();				
			}
		},
		{
			id: "button-dettaglio-nuova-scadenza-form",
			text: "Nuova scadenza",
			click: function() {	
				$("#datascad_cre" ).val("");
				$("#newimpscad_cre").val("");
				$("#button-nuova-scadenza-form").button("disable");
				$("#nuova-data-scadenza-form").dialog( "open" );
			}
		},
		{
			id: "button-dettaglio-nuova-registrazione-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-nuovo-dettaglio-form").button("disable");
				$("#importo_dett_cre").val("");
				$("#nuovo-dettaglio-registrazione-form").dialog( "open" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	document.getElementById("nuovaRegistrazione").reset();
		            	$("#tddettagli_cre").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagli").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaNuovaRegistrazioneFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});

$( "#nuovo-dettaglio-registrazione-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 250,
	buttons: [
		{
			id: "button-Ok-nuovo-dettaglio-form",
			text: "Ok",
			click: function() {

				if($('#dare_dett_cre').is(':checked')) { var D_A = $("#dare_dett_cre").val(); }
				if($('#avere_dett_cre').is(':checked')) { var D_A = $("#avere_dett_cre").val(); }

				var conto = $("#conti").val().replace(",",".");			// tolgo eventuali virgole nella descrizione del conto	
				var idconto = conto.substring(0, 6);
				var importo = $("#importo_dett_cre").val();
				var importoNormalizzato = importo.trim().replace(",", ".");
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	var sottocontiTable = xmlhttp.responseText;
		        		$("#dettagli_cre").html(sottocontiTable);
		        		$("#dettagli_mod").html(sottocontiTable);
		        		controllaDettagliRegistrazione("tddettagli_cre","messaggioControlloDettagli","descreg_cre","descreg_cre_label");
		        		controllaDettagliRegistrazione("tddettagli_mod","messaggioControlloDettagli_mod","descreg_mod","descreg_mod_label");
		        	}
			    }
			    xmlhttp.open("GET", "aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
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
// Modifica di una registrazione
//---------------------------------------------------------------------------------		
$( "#modifica-registrazione-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 1000,
	buttons: [
		{
			id: "button-ok-modifica-registrazione-form",
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaRegistrazione").submit();				
			}
		},
		{
			id: "button-dettaglio-nuova-scadenza-form",
			text: "Nuova scadenza",
			click: function() {	
				$("#datascad_mod" ).val("");
				$("#newimpscad_mod").val("");
				$("#button-nuova-scadenza-modifica-form").button("disable");
				$("#nuova-data-scadenza-modifica-form").dialog( "open" );
			}
		},
		{
			id: "button-dettaglio-modifica-registrazione-form",
			text: "Nuovo Dettaglio",
			click: function() {				
				$("#button-Ok-nuovo-dettaglio-form").button("disable");
				$("#importo_dett_cre").val("");
				$("#nuovo-dettaglio-registrazione-form").dialog( "open" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
				
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			        	document.getElementById("modificaRegistrazione").reset();
		            	$("#tddettagli_mod").removeClass("inputFieldError");	
		    			$("#messaggioControlloDettagli_mod").html("");			
			        }
			    }
			    xmlhttp.open("GET", "annullaModificaRegistrazioneFacade.class.php?modo=start", true);
			    xmlhttp.send();		
			}
		}
	]
});

//---------------------------------------------------------------------------------		
// CREA REGISTRAZIONE : controllo campi in pagina
//---------------------------------------------------------------------------------		

function validaNuovaRegistrazione()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_cre", "tddatareg_cre", "messaggioControlloDataRegistrazione");
	if ($("#messaggioControlloDataRegistrazione").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_cre").val() != "") {
		if (controllaDescrizione("descreg_cre", "tddescreg_cre", "messaggioControlloDescrizione")) 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if (controllaClienteFornitore("fornitore_cre", "cliente_cre", "tdfornitore_cre", "tdcliente_cre", "tdnumfatt_cre")) 
		esito = esito + "1"; else esito = esito + "0";
	
	if (($("#fornitore_cre").val() != "") || $("#cliente_cre").val() != "") {
		if (controllaNumeroFattura("numfatt_cre", "tdnumfatt_cre")) 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if ($("#fornitore_cre").val() != "") {
		controllaNumeroFatturaFornitore("fornitore_cre", "numfatt_cre", "datareg_cre", "tdnumfatt_cre", "messaggioControlloNumeroFattura");
		if ($("#messaggioControlloNumeroFattura").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#cliente_cre").val() != "") {
		controllaNumeroFatturaCliente("cliente_cre", "numfatt_cre", "datareg_cre", "tdnumfatt_cre", "messaggioControlloNumeroFattura");
		if ($("#messaggioControlloNumeroFattura").text() == "") esito = esito + "1"; else esito = esito + "0";
	}

	if ($("#causale_cre").val() != "") {
		controllaDettagliRegistrazione("tddettagli_cre","messaggioControlloDettagli","descreg_cre","descreg_cre_label");
		if ($("#messaggioControlloDettagli").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
		
	if (esito == "111111") {
		$("#button-ok-nuova-registrazione-form").button("enable");
	} else {
		$("#button-ok-nuova-registrazione-form").button("disable");	
	}
}

//---------------------------------------------------------------------------------		

function controllaImportoDettaglio() {
	
	var importo = $("#importo_dett_cre").val();
	
	if (isNumeric(importo)) {
       	$("#importo_dett_cre").removeClass("inputFieldError");
		$("#messaggioControlloImportoDettaglio").html("");		
		$("#button-Ok-nuovo-dettaglio-form").button("enable");	
	}
	else {		
       	$("#importo_dett_cre").addClass("inputFieldError");
		$("#messaggioControlloImportoDettaglio").html("Importo non valido");		
		$("#button-Ok-nuovo-dettaglio-form").button("disable");
	}	
}

//---------------------------------------------------------------------------------		

function controllaDataRegistrazione(campoDat, campoDatErr, campoMsg)
{
	/**
	 * La data registrazione è obbligatoria
	 * Il controllo sulla data registrazione verificha che la data immessa cada all'interno
	 * di uno dei mesi in linea. I mesi in linea coincidono con le date pianificate di riporto saldo
	 * 
	 */
	var datareg = $("#" + campoDat).val();
	
	if (datareg != "") {
		
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {	            
	            if (xmlhttp.responseText != "") {
		            $("#" + campoMsg).html(xmlhttp.responseText);	            	
	            	$("#" + campoDatErr).addClass("inputFieldError");
	            }
	            else {
		            $("#" + campoMsg).html("");	            	
	            	$("#" + campoDatErr).removeClass("inputFieldError");	
	            }
	        }
	    }
	    xmlhttp.open("GET", "controllaDataRegistrazioneFacade.class.php?modo=start&datareg=" + datareg, true);
	    xmlhttp.send();		
	}
	else {
		$("#" + campoMsg).html("Dato errato");
		$("#" + campoDatErr).addClass("inputFieldError");
	}
}

//---------------------------------------------------------------------------------		

function controllaDescrizione(campoDes, campoDesErr, campoMsg)
{
	/**
	 * La descrizione della registrazione è obbligatoria
	 */
	if ($("#" + campoDes).val() != "") {
		$("#" + campoDesErr).removeClass("inputFieldError");	
		$("#" + campoMsg).html("");
		return true;
	}
	else {
		$("#" + campoDesErr).addClass("inputFieldError");
		$("#" + campoMsg).html("Dato errato");
		return false;
	}
}

//---------------------------------------------------------------------

function controllaClienteFornitore(campoForn, campoCli, campoFornErr, campoCliErr, campoFatErr)
{
	/**
	 * Il cliente e il fornitore sono mutualmente esclusivi
	 * Possono mancare entrambi
	 */
	if (($("#" + campoForn).val() != "") && ($("#" + campoCli).val() != "")) {

		$("#" + campoFornErr).addClass("inputFieldError");			
		$("#" + campoCliErr).addClass("inputFieldError");							
		$("#messaggioControlloFornitore" ).html("Dato errato");
		$("#messaggioControlloCliente" ).html("Dato errato");
		$("#button-dettaglio-nuova-scadenza-form").button("disable");
		$("#scadenzesuppl_cre").hide();
		$("#datascad_cre_label").hide();
		$("#numfatt_cre_label").hide();
		$("#numfatt_cre").hide();
		return false;
	}
	else if (($("#" + campoForn).val() == "") && ($("#" + campoCli).val() == "")){
		$("#datascad_cre_label").hide();
		$("#button-dettaglio-nuova-scadenza-form").button("disable");
		$("#scadenzesuppl_cre").hide();
		$("#datascad_cre_label").hide();
		$("#numfatt_cre_label").hide();
		$("#numfatt_cre").hide();
		$("#" + campoFatErr).removeClass("inputFieldError");	
		$("#messaggioControlloNumeroFattura" ).html("");
		return false;		
	}
	else {
		$("#" + campoFornErr).removeClass("inputFieldError");	
		$("#" + campoCliErr).removeClass("inputFieldError");	
		$("#" + campoFatErr).removeClass("inputFieldError");	
		$("#messaggioControlloFornitore" ).html("");
		$("#messaggioControlloCliente" ).html("");				
		$("#messaggioControlloNumeroFattura" ).html("");
		$("#button-dettaglio-nuova-scadenza-form").button("enable");
		$("#scadenzesuppl_cre").show();
		$("#datascad_cre_label").show();
		$("#numfatt_cre_label").show();
		$("#numfatt_cre").show();
		return true;		
	}
}

//---------------------------------------------------------------------

function controllaNumeroFattura(campoFat, campoFatErr)
{
	var numfatt = $("#" + campoFat).val();

	if (numfatt != "") {
		$("#" + campoFatErr).removeClass("inputFieldError");			
		$("#messaggioControlloNumeroFattura" ).html("");
		return true;
	}
	else {
		$("#" + campoFatErr).addClass("inputFieldError");			
		$("#messaggioControlloNumeroFattura" ).html("Immetti in numero fattura");
		return false;
	}
}

//---------------------------------------------------------------------

function controllaNumeroFatturaFornitore(campoForn, campoFat, campoDat, campoFattErr, campoMsg)
{
	/**
	 * La fattura del fornitore immessa deve essere univoca
	 */
	var fornitore = $("#" + campoForn).val();
	var numfatt = $("#" + campoFat).val();
	var numfattOrig = $("#" + campoFat + "_orig").val();
	var datareg = $("#" + campoDat).val();
	
	if ((numfatt != "") && (datareg != "") ) {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {	            
	            if (xmlhttp.responseText != "") {
	            	if (numfatt != numfattOrig) {		            	
		            	$("#" + campoFattErr).addClass("inputFieldError");
			            $("#" + campoMsg).html(xmlhttp.responseText);	            		
	            	}
	            	else {
		            	$("#" + campoFattErr).removeClass("inputFieldError");	
			            $("#" + campoMsg).html("");
	            	}
	            }
	            else {
	            	$("#" + campoFattErr).removeClass("inputFieldError");	
		            $("#" + campoMsg).html("");
	            }	            
	        }
	    } 
	    xmlhttp.open("GET", "cercaFatturaFornitoreFacade.class.php?modo=start&desfornitore=" + fornitore + "&numfatt=" + numfatt + "&datareg=" + datareg, true);
	    xmlhttp.send();		
	}
	else return true;
}

//---------------------------------------------------------------------

function controllaNumeroFatturaCliente(campoCli, campoFat, campoDat, campoFattErr, campoMsg)
{
	/**
	 * La fattura del fornitore immessa deve essere univoca
	 */
	var cliente = $("#" + campoCli).val();
	var numfatt = $("#" + campoFat).val();
	var datareg = $("#" + campoDat).val();
	
	if ((numfatt != "") && (datareg != "")) {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") { 
	            	$("#" + campoFattErr).addClass("inputFieldError");
		            $("#" + campoMsg).html(xmlhttp.responseText);
	            }
	            else {
	            	$("#" + campoFattErr).removeClass("inputFieldError");	
		            $("#" + campoMsg).html("");
	            }	            
	        }
	    } 
	    xmlhttp.open("GET", "cercaFatturaClienteFacade.class.php?modo=start&descliente=" + cliente + "&numfatt=" + numfatt + "&datareg=" + datareg, true);
	    xmlhttp.send();		
	}
	else return true;
}

//---------------------------------------------------------------------

function controllaDettagliRegistrazione(campoDetErr,campoMsg,campoDes,campoDesLabel)
{
	/**
	 * I dettagli della registrazione devono essere presenti
	 * Gli importi del Dare e Avere devono quadrare
	 */
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
            if (xmlhttp.responseText != "") { 
            	$("#" + campoDetErr).addClass("inputFieldError");
    			$("#" + campoMsg).html("Completa i dettagli");		
    			$("#button-ok-nuova-registrazione-form").button("disable");
    			$("#button-ok-modifica-registrazione-form").button("disable");
            }
            else {
            	$("#" + campoDes).show();
            	$("#" + campoDesLabel).show();            	
            	$("#" + campoDetErr).removeClass("inputFieldError");	
    			$("#" + campoMsg).html("");			
    			$("#button-ok-nuova-registrazione-form").button("enable");
    			$("#button-ok-modifica-registrazione-form").button("enable");
            }	            
        }
    } 
    xmlhttp.open("GET", "verificaDettagliRegistrazioneFacade.class.php?modo=start", true);
    xmlhttp.send();		
}

function modificaImportoDettaglioRegistrazione(conto,sottoconto,importo,idDettaglio)
{
	if (importo == "") var importoDett = 0;
	else var importoDett = importo;
	
	if (conto != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") { 
		        	var dettagliTable = xmlhttp.responseText;
	        		$("#dettagli_cre").html(dettagliTable);			// creazione registrazione
	        		$("#dettagli_mod").html(dettagliTable);			// modifica registrazione
	        		$("#dettagli_inc_cre").html(dettagliTable);		// incassi
	        		$("#dettagli_pag_cre").html(dettagliTable);		// pagamenti
	        		$("#dettagli_cormer_cre").html(dettagliTable);	// corrispettivi mercato
	        		$("#dettagli_corneg_cre").html(dettagliTable);	// corrispettivi negozio
	        		controllaDettagliRegistrazione("tddettagli_cre","messaggioControlloDettagli","descreg_cre","descreg_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_mod","messaggioControlloDettagli_mod","descreg_mod","descreg_mod_label");
	        		controllaDettagliRegistrazione("tddettagli_inc_cre","messaggioControlloDettagliIncasso","descreg_inc_cre","descreg_inc_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_pag_cre","messaggioControlloDettagliPagamento","descreg_pag_cre","descreg_pag_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_cormer_cre","messaggioControlloDettagliCorrispettivoMercato","descreg_cormer_cre","descreg_cormer_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_corneg_cre","messaggioControlloDettagliCorrispettivoNegozio","descreg_corneg_cre","descreg_corneg_cre_label");
	            }
	        }
	    }
	    xmlhttp.open("GET", "aggiornaImportoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&importo=" + importoDett + "&iddettaglio=" + idDettaglio, true);
	    xmlhttp.send();
	}
}

function modificaSegnoDettaglioRegistrazione(conto,sottoconto,segno,idDettaglio)
{
	if (conto != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") {
		        	var dettagliTable = xmlhttp.responseText;
	        		$("#dettagli_cre").html(dettagliTable);			// creazione registrazione
	        		$("#dettagli_mod").html(dettagliTable);			// modifica registrazione
	        		$("#dettagli_inc_cre").html(dettagliTable);		// incasso
	        		$("#dettagli_pag_cre").html(dettagliTable);		// pagamento
	        		$("#dettagli_cormer_cre").html(dettagliTable);	// corrispettivi mercato
	        		$("#dettagli_corneg_cre").html(dettagliTable);	// corrispettivi negozio
	        		controllaDettagliRegistrazione("tddettagli_cre","messaggioControlloDettagli","descreg_cre","descreg_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_mod","messaggioControlloDettagli_mod","descreg_mod","descreg_mod_label");
	        		controllaDettagliRegistrazione("tddettagli_inc_cre","messaggioControlloDettagliIncasso","descreg_inc_cre","descreg_inc_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_pag_cre","messaggioControlloDettagliPagamento","descreg_pag_cre","descreg_pag_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_cormer_cre","messaggioControlloDettagliCorrispettivoMercato","descreg_cormer_cre","descreg_cormer_cre_label");
	        		controllaDettagliRegistrazione("tddettagli_corneg_cre","messaggioControlloDettagliCorrispettivoNegozio","descreg_corneg_cre","descreg_corneg_cre_label");
	            }
	        }
	    }
	    xmlhttp.open("GET", "aggiornaSegnoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&dareAvere=" + segno + "&iddettaglio=" + idDettaglio, true);
	    xmlhttp.send();
	}
}

function aggiungiDettaglioContoFornitore(fornitore,campoDett,campoMsg,campoDes,campoDesLabel)
{
	if (fornitore != "")
	{
		var fornitoreNorm = fornitore.replace("&","");		// tolgo eventuali & nella ragione sociale

		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") {
		        	var dettagliTable = xmlhttp.responseText;
	        		$("#" + campoDett).html(dettagliTable);
	        		$("#" + campoDett).show();
	        		controllaDettagliRegistrazione(campoDett,campoMsg,campoDes,campoDesLabel);
	            }
	        }
	    }
	    xmlhttp.open("GET", "aggiungiNuovoDettaglioContoFornitoreFacade.class.php?modo=go&desfornitore=" + fornitoreNorm, true);
	    xmlhttp.send();
	}
}

function aggiungiDettaglioContoCliente(cliente,campoDett,campoMsg,campoDes,campoDesLabel)
{
	if (cliente != "") {
		var clienteNorm = cliente.replace("&","");		// tolgo eventuali & nella ragione sociale

		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") {
		        	var dettagliTable = xmlhttp.responseText;
	        		$("#" + campoDett ).html(dettagliTable);
	        		$("#" + campoDett).show();
	        		controllaDettagliRegistrazione(campoDett,campoMsg,campoDes,campoDesLabel);
	            }
	        }
	    }
	    xmlhttp.open("GET", "aggiungiNuovoDettaglioContoClienteFacade.class.php?modo=go&descliente=" + clienteNorm, true);
	    xmlhttp.send();		
	}
}


//---------------------------------------------------------------------------------		
// MODIFICA REGISTRAZIONE : controllo campi in pagina
//---------------------------------------------------------------------------------		

function validaModificaRegistrazione()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
	 * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
	 * Se la validazione è positiva viene abilitato il bottone ok di conferma inserimento
	 */
	var esito = "";
	
	controllaDataRegistrazione("datareg_mod", "tddatareg_mod", "messaggioControlloDataRegistrazione_mod");
	if ($("#messaggioControlloDataRegistrazione_mod").text() == "") 
		esito = esito + "1"; else esito = esito + "0";

	if ($("#descreg_mod").val() != "") {
		if (controllaDescrizione("descreg_mod", "tddescreg_mod", "messaggioControlloDescrizione_mod")) 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if (controllaClienteFornitore("fornitore_mod", "cliente_mod", "tdfornitore_mod", "tdcliente_mod", "tdnumfatt_mod")) 
		esito = esito + "1"; else esito = esito + "0";
	
	if (($("#fornitore_mod").val() != "") || $("#cliente_mod").val() != "") {
		if (controllaNumeroFattura("numfatt_mod", "tdnumfatt_mod")) 
			esito = esito + "1"; else esito = esito + "0";		
	}
	
	if ($("#fornitore_mod").val() != "") {
		controllaNumeroFatturaFornitore("fornitore_mod", "numfatt_mod", "datareg_mod", "tdnumfatt_mod", "messaggioControlloNumeroFattura_mod");
		if ($("#messaggioControlloNumeroFattura_mod").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}

	if ($("#cliente_mod").val() != "") {
		controllaNumeroFatturaCliente("cliente_mod", "numfatt_mod", "datareg_mod", "tdnumfatt_mod", "messaggioControlloNumeroFattura_mod");
		if ($("#messaggioControlloNumeroFattura_mod").text() == "") esito = esito + "1"; else esito = esito + "0";
	}

	if ($("#causale_mod").val() != "") {
		controllaDettagliRegistrazione("tddettagli_mod","messaggioControlloDettagli_mod","descreg_mod","descreg_mod_label");
		if ($("#messaggioControlloDettagli_mod").text() == "") 
			esito = esito + "1"; else esito = esito + "0";		
	}
		
	if (esito == "111111") {
		$("#button-ok-modifica-registrazione-form").button("enable");
	} else {
		$("#button-ok-modifica-registrazione-form").button("disable");	
	}
}

//---------------------------------------------------------------------

function modificaRegistrazione(idRegistrazione)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {    	  
			var response = xmlhttp.responseText;
			var datiPagina = response.split("|");
			var datareg_mod = datiPagina[0];
			var descreg_mod = datiPagina[1];
			var causale_mod = datiPagina[2];
			var codneg_mod  = datiPagina[3];
			var fornitore_mod = datiPagina[4];
			var cliente_mod = datiPagina[5];
			var numfatt_mod = datiPagina[6];
			var scadenzesuppl_fornitore_mod = datiPagina[7];
			var scadenzesuppl_cliente_mod = datiPagina[8];
			var dettagli_mod = datiPagina[9];
			var conti = datiPagina[10];
			
			$("#datareg_mod").val(datareg_mod);
			$("#descreg_mod").val(descreg_mod);
			$("#causale_mod").val(causale_mod);
            $("#causale_mod").selectmenu( "refresh" );
			
			if (codneg_mod == "VIL") {
				$("#villa_mod").prop("checked", true).button("refresh");
			}
			else {
	    		if (codneg_mod == "BRE") {
	    			$("#brembate_mod").prop("checked", true).button("refresh");
	    		}
	    		else {
	        		if (codneg_mod == "TRE") {
	        			$("#trezzo_mod").prop("checked", true).button("refresh");
	        		}
	    		}
			}        	

			$("#fornitore_mod").val(fornitore_mod);
			$("#cliente_mod").val(cliente_mod);			
			$("#numfatt_mod").val(numfatt_mod);
			$("#numfatt_mod_orig").val(numfatt_mod);

			if (fornitore_mod != "") $("#scadenzesuppl_mod").html(scadenzesuppl_fornitore_mod);
			if (cliente_mod != "")   $("#scadenzesuppl_mod").html(scadenzesuppl_cliente_mod);
			
			$("#dettagli_mod").html(dettagli_mod);
            $("#conti").html(conti);
            $("#conti").selectmenu( "refresh" );
			
			$( "#modifica-registrazione-form" ).dialog( "open" );
		}
	} 
	xmlhttp.open("GET", "modificaRegistrazioneFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
	xmlhttp.send();		
}

//---------------------------------------------------------------------------------				
//  funzioni x le scadenze
//---------------------------------------------------------------------------------				

$( "#nuova-data-scadenza-form" ).dialog( {
	autoOpen: false,
	modal: true,
	width: 550,
	height: 180,
	buttons: [
		{
			id: "button-nuova-scadenza-form",
			text: "Ok",
			click: function() {

				var datascad = $("#newdatascad_cre").val();				
				var impscad  = $("#newimpscad_cre").val();
				var fornitore = $("#fornitore_cre").val();
				var cliente = $("#cliente_cre").val();
				var numfatt = $("#numfatt_cre").val();
				
				if (fornitore != "")
				{	
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				            if (xmlhttp.responseText != "") {
				            	$("#datascad_cre_label").show();
				        		$("#scadenzesuppl_cre").html(xmlhttp.responseText);
				            }
				            else {
				            	$("#datascad_cre_label").hide();
				        		$("#scadenzesuppl_cre").html("");
				        		$("#scadenzesuppl_cre").hide();
				            }	            
				        }				        
				    } 
				    xmlhttp.open("GET", "aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore=" + fornitore + "&datascad_for=" + datascad + "&impscad_for=" + impscad + "&numfatt=" + numfatt, true);
				    xmlhttp.send();
				}
				else if (cliente != "")
				{
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				            if (xmlhttp.responseText != "") { 
				            	$("#datascad_cre_label").show();
				        		$("#scadenzesuppl_cre").html(xmlhttp.responseText);
				            }
				            else {
				            	$("#datascad_cre_label").hide();
				        		$("#scadenzesuppl_cre").html("");
				        		$("#scadenzesuppl_cre").hide();
				            }	            
				        }
				    } 
				    xmlhttp.open("GET", "aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&datascad_cli=" + datascad + "&cliente=" + cliente + "&impscad_cli=" + impscad + "&numfatt=" + numfatt, true);
				    xmlhttp.send();	
				} 
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

$( "#nuova-data-scadenza-modifica-form" ).dialog( {
	autoOpen: false,
	modal: true,
	width: 550,
	height: 180,
	buttons: [
		{
			id: "button-nuova-scadenza-modifica-form",
			text: "Ok",
			click: function() {

				var datascad = $("#newdatascad_mod").val();				
				var impscad  = $("#newimpscad_mod").val();
				var fornitore = $("#fornitore_mod").val();
				var cliente = $("#cliente_mod").val();
				var numfatt = $("#numfatt_mod").val();
				
				if (fornitore != "")
				{	
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				            if (xmlhttp.responseText != "") {
				            	$("#datascad_mod_label").show();
				        		$("#scadenzesuppl_mod").html(xmlhttp.responseText);
				            }
				            else {
				            	$("#datascad_mod_label").hide();
				        		$("#scadenzesuppl_mod").html("");
				        		$("#scadenzesuppl_mod").hide();
				            }	            
				        }				        
				    } 
				    xmlhttp.open("GET", "aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore=" + fornitore + "&datascad_for=" + datascad + "&impscad_for=" + impscad + "&numfatt=" + numfatt, true);
				    xmlhttp.send();
				}
				else if (cliente != "")
				{
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				            if (xmlhttp.responseText != "") { 
				            	$("#datascad_mod_label").show();
				        		$("#scadenzesuppl_mod").html(xmlhttp.responseText);
				            }
				            else {
				            	$("#datascad_mod_label").hide();
				        		$("#scadenzesuppl_mod").html("");
				        		$("#scadenzesuppl_mod").hide();
				            }	            
				        }
				    } 
				    xmlhttp.open("GET", "aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&datascad_cli=" + datascad + "&cliente=" + cliente + "&impscad_cli=" + impscad + "&numfatt=" + numfatt, true);
				    xmlhttp.send();	
				} 
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

function controllaImportoScadenza(campoImp, campoMsg, idButtonOk)
{	
	var importo = $("#" + campoImp).val();
	
	if (isNumeric(importo)) {
       	$("#" + campoImp).removeClass("inputFieldError");
		$("#" + campoMsg).html("");		
		$("#" + idButtonOk).button("enable");	
	}
	else {		
       	$("#" + campoImp).addClass("inputFieldError");
		$("#" + campoMsg).html("Importo non valido");		
		$("#" + idButtonOk).button("disable");
	}	
}

//---------------------------------------------------------------------------------	

function cancellaNuovaScadenzaFornitore(idFornitore, datScad, numFatt)
{
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
    		$("#scadenzesuppl_cre").html(xmlhttp.responseText);
    		$("#scadenzesuppl_mod").html(xmlhttp.responseText);
        }
    } 
    xmlhttp.open("GET", "cancellaScadenzaFornitoreFacade.class.php?modo=start&idfornitore=" + idFornitore + "&datascad_for=" + datScad + "&numfatt=" + numFatt, true);
    xmlhttp.send();		
}

//---------------------------------------------------------------------------------	

function cancellaNuovaScadenzaCliente(idCliente, datScad, numFatt)
{
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
    		$("#scadenzesuppl_cre").html(xmlhttp.responseText);
        }
    } 
    xmlhttp.open("GET", "cancellaScadenzaClienteFacade.class.php?modo=start&idcliente=" + idCliente + "&datascad_cli=" + datScad + "&numfatt=" + numFatt, true);
    xmlhttp.send();		
}

//---------------------------------------------------------------------------------	

function modificaImportoScadenzaFornitore(idfornitore,datascad,numfatt,importo)
{
	if (importo == "") var importoScad = 0;
	else var importoScad = importo;

	if (datascad != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") { 
		        	var scadenzeTable = xmlhttp.responseText;
		    		$("#scadenzesuppl_cre").html(scadenzeTable);
		    		$("#scadenzesuppl_mod").html(scadenzeTable);
	            }
	        }
	    }
	    xmlhttp.open("GET", "aggiornaImportoScadenzaFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore + "&datascad_for=" + datascad + "&numfatt=" + numfatt + "&impscad_for=" + importoScad, true);
	    xmlhttp.send();
	}
}

//---------------------------------------------------------------------------------	

function modificaImportoScadenzaCliente(idcliente,datascad,numfatt,importo)
{
	if (importo == "") var importoScad = 0;
	else var importoScad = importo;

	if (datascad != "") {
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	            if (xmlhttp.responseText != "") { 
		        	var scadenzeTable = xmlhttp.responseText;
		    		$("#scadenzesuppl_cre").html(scadenzeTable);
	            }
	        }
	    }
	    xmlhttp.open("GET", "aggiornaImportoScadenzaClienteFacade.class.php?modo=go&idcliente=" + idcliente + "&datascad_cli=" + datascad + "&numfatt=" + numfatt + "&impscad_cli=" + importoScad, true);
	    xmlhttp.send();
	}
}

//---------------------------------------------------------------------------------	
// Funzioni per clienti e fornitori	
//---------------------------------------------------------------------------------	

$( "#fornitore_cre" ).change(function()	{
	
	var desfornitore = $("#fornitore_cre").val();
	var datareg = $("#datareg_cre").val();
	
	if (desfornitore != "") {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	        	if (xmlhttp.responseText != "") {
	            	$("#datascad_cre_label").show();
	          		$("#scadenzesuppl_cre").html(xmlhttp.responseText);        		
	        	}
	        }
		}
		xmlhttp.open("GET", "calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore=" + desfornitore + "&datareg=" + datareg, true);
		xmlhttp.send();						
	}
});


//---------------------------------------------------------------------------------	
// Select Menu
//---------------------------------------------------------------------------------	

$( ".selectmenuCausaleCre" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_cre").val();
			
			if (causale != "") {
	        	$( "#tdcausale_cre").removeClass("inputFieldError");	
				$( "#messaggioControlloCausale" ).html("");
				
				var xmlhttp = new XMLHttpRequest();
		        xmlhttp.onreadystatechange = function() {
		            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		                $( "#conti" ).html(xmlhttp.responseText);
		                $( "#conti" ).selectmenu( "refresh" );
		            	$("#button-dettaglio-nuova-registrazione-form").button("enable");	                
		            	validaNuovaRegistrazione();
		            }
		        }
		        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
		        xmlhttp.send();			
								
			}
			else {
				$("#tdcausale_cre").addClass("inputFieldError");	
				$("#messaggioControlloCausale").html("Dato errato");
          	$("#button-dettaglio-nuova-registrazione-form").button("disable");	                
			}
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------			

$( ".selectmenuCausaleMod" )
	.selectmenu({change:
		function(){
			var causale = $("#causale_mod").val();
			
			if (causale != "") {
	        	$( "#tdcausale_mod").removeClass("inputFieldError");	
				$( "#messaggioControlloCausale_mod" ).html("");
				
				var xmlhttp = new XMLHttpRequest();
		        xmlhttp.onreadystatechange = function() {
		            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		                $( "#conti" ).html(xmlhttp.responseText);
		                $( "#conti" ).selectmenu( "refresh" );
		            	$("#button-dettaglio-modifica-registrazione-form").button("enable");	                
		            	validaModificaRegistrazione();
		            }
		        }
		        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
		        xmlhttp.send();			
								
			}
			else {
				$("#tdcausale_mod").addClass("inputFieldError");	
				$("#messaggioControlloCausale_mod").html("Dato errato");
				$("#button-dettaglio-modifica-registrazione-form").button("disable");	                
			}
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------

function cancellaDettaglioNuovaRegistrazione(codConto) {

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var dettagliTable = xmlhttp.responseText;
    		$("#dettagli_cre").html(dettagliTable);			// creazione registrazione
    		$("#dettagli_mod").html(dettagliTable);			// modifica registrazione
    		$("#dettagli_inc_cre").html(dettagliTable);		// incasso
    		$("#dettagli_pag_cre").html(dettagliTable);		// pagamento
    		$("#dettagli_cormer_cre").html(dettagliTable);	// corrispettivi mercato
    		$("#dettagli_corneg_cre").html(dettagliTable);	// corrispettivi negozio
    		controllaDettagliRegistrazione("tddettagli_cre","messaggioControlloDettagli","descreg_cre","descreg_cre_label");
    		controllaDettagliRegistrazione("tddettagli_mod","messaggioControlloDettagli_mod","descreg_mod","descreg_mod_label");
    		controllaDettagliRegistrazione("tddettagli_inc_cre","messaggioControlloDettagliIncasso","descreg_inc_cre","descreg_inc_cre_label");
    		controllaDettagliRegistrazione("tddettagli_pag_cre","messaggioControlloDettagliPagamento","descreg_pag_cre","descreg_pag_cre_label");
    		controllaDettagliRegistrazione("tddettagli_cormer_cre","messaggioControlloDettagliCorrispettivoMercato","descreg_cormer_cre","descreg_cormer_cre_label");
    		controllaDettagliRegistrazione("tddettagli_corneg_cre","messaggioControlloDettagliCorrispettivoNegozio","descreg_corneg_cre","descreg_corneg_cre_label");
		}
	}
	xmlhttp.open("GET", "cancellaNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + codConto, true);
	xmlhttp.send();				
}












//---------------------------------------------------------------------------------				
// vecchie funzioni non ancor utilizzate dalla nuova versione 4.0
//---------------------------------------------------------------------------------				

$( "#nuovo-dett-modificareg" ).click(function( event ) {
	$( "#nuovo-dettaglio-modificareg-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuovo-dettaglio-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 400,
	buttons: [
		{
			text: "Ok",
			click: function() {
				
				// Controllo congruenza conto dettaglio
				
				var conto = $("#conti").val();
				var fornitore = $("#fornitore").val();
				var cliente = $("#cliente").val();

				// Fornitore
				
				if (fornitore != "") {

					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				        	if (xmlhttp.responseText == "Dettaglio ok") {
								
					            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
				                $("#nuovoDettaglio").submit();				
				        		
				        	}
				        	else {
				        		$( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
				        	}
				        }		
				        
				    } 
				    xmlhttp.open("GET", "controlloContoDettaglioPagamentoFacade.class.php?modo=start&fornitore=" + fornitore + "&conto=" + conto, true);
				    xmlhttp.send();
				}

				// Cliente
				
				else if (cliente != "") {

					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				        	if (xmlhttp.responseText == "Dettaglio ok") {
								
					            $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
				                $("#nuovoDettaglio").submit();				
				        		
				        	}
				        	else {
					            $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
				        	}
				        }		
				        
				    } 
				    xmlhttp.open("GET", "controlloContoDettaglioIncassoFacade.class.php?modo=start&cliente=" + cliente + "&conto=" + conto, true);
				    xmlhttp.send();
				}
				else {
	                $("#nuovoDettaglio").submit();				
				}
				
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

$( "#nuova-scad-modificareg" ).click(function( event ) {
	$( "#nuova-scadenza-modificareg-form" ).dialog( "open" );
	event.preventDefault();
});

$( "#nuova-scadenza-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 550,
	height: 150,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
           $("#nuovaScadenza").submit();				
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

function cancellaDettaglio(idconto) {
	$( "#idDettaglioRegistrazione" ).val(idconto);
	$( "#cancella-dettaglio-modificareg-form" ).dialog( "open" );
}
$( "#cancella-dettaglio-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
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

//---------------------------------------------------------------------------------				

function cancellaRegistrazione(idreg) {	
	$( "#idRegistrazione" ).val(idreg);
	$( "#cancella-registrazione-form" ).dialog( "open" );
}

$( "#cancella-registrazione-form" ).dialog({
	autoOpen: false,
	modal: true,
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

//---------------------------------------------------------------------------------				
function cancellaScadenza(idscadenza) {
//---------------------------------------------------------------------------------	
	$( "#idScadenzaRegistrazione" ).val(idscadenza);
	$( "#cancella-scadenza-modificareg-form" ).dialog( "open" );
}

$( "#cancella-scadenza-modificareg-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
         $("#cancellaScadenza").submit();				
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

$( "#fornitore" ).change(function() {
	
	var desfornitore = $("#fornitore").val();
	var datareg = $("#datareg").val();
	var form = $("#pagamentoForm").val();
	
	if (desfornitore != "") {

		if (form == "PAGAMENTO") {
			
			var xmlhttp = new XMLHttpRequest();
		    xmlhttp.onreadystatechange = function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		            $( "#select2" ).html(xmlhttp.responseText);
		            $( "#select2" ).selectmenu( "refresh" );
		        }
		    }
		    xmlhttp.open("GET", "ricercaScadenzeAperteFornitoreFacade.class.php?modo=start&desforn=" + desfornitore, true);
		    xmlhttp.send();		
		}
		else {
			
			/**
			 * Data scadenza
			 */			
			var xmlhttp = new XMLHttpRequest();
	        xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	                $( "#datascad" ).val(xmlhttp.responseText);
	            	$( "#tddatascad").removeClass("inputFieldError");	
	                $( "#esitoDatascad" ).val("");			
	        		$( "#messaggioControlloDataScadenza" ).html("");	                
	            }
	        }
	        xmlhttp.open("GET", "calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore=" + desfornitore + "&datareg=" + datareg, true);
	        xmlhttp.send();						
		}		
	}
});

//---------------------------------------------------------------------------------	

$( "#fornitore_regrap" ).change(function() {
	
	var desfornitore = $("#fornitore_regrap").val();
	
	if (desfornitore != "") {
		/**
		 * Genero i dettagli della registrazione
		 */
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			$( "#dettagli" ).html(xmlhttp.responseText);	                
        }
	}
	xmlhttp.open("GET", "aggiungiFornitoreDettagliRegistrazioneFacade.class.php?modo=start&desfornitore=" + desfornitore, true);
	xmlhttp.send();						
	}		
});

//---------------------------------------------------------------------------------	

$( "#cliente_regrap" ).change(function() {
	
	var descliente = $("#cliente_regrap").val();
	
	if (descliente != "") {
		/**
		 * Genero i dettagli della registrazione
		 */
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
    		$( "#dettagli" ).html(xmlhttp.responseText);	                
        }
    }
	xmlhttp.open("GET", "aggiungiClienteDettagliRegistrazioneFacade.class.php?modo=start&descliente=" + descliente, true);
	xmlhttp.send();						
	}		
});

//---------------------------------------------------------------------------------	

$( "#cliente" ).change(function() {
	
	var descliente = $("#cliente").val();

	if (descliente != "") {
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $( "#tipoadd" ).val(xmlhttp.responseText);
            }
        }
        xmlhttp.open("GET", "prelevaTipoAddebitoClienteFacade.class.php?modo=start&descliente=" + descliente, true);
        xmlhttp.send();			
	}
})

//---------------------------------------------------------------------------------	

$( ".selectmenuCausale" )
	.selectmenu({width: 350})
	.selectmenu("menuWidget")
	.addClass("overflow");

$( ".selectmenuCausaleCre" )
	.selectmenu({width: 350})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------	

$('#numeroFatturaFornitore').change(function() {
	var fornitore = $("#fornitore").val();
	var numfatt = $("#numfatt").val();
	var causale = $("#causale").val();
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $( "#esitoControlloNumeroFattura" ).html(xmlhttp.responseText);
        }
    } 
    xmlhttp.open("GET", "cercaFatturaFornitoreFacade.class.php?modo=start&idfornitore=" + fornitore + "&numfatt=" + numfatt, true);
    xmlhttp.send();				
});
