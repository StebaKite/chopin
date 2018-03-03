//---------------------------------------------------------------------------------				
// Registrazioni
//---------------------------------------------------------------------------------				

//---------------------------------------------------------------------------------		
// Creazione di una nuova registrazione
//---------------------------------------------------------------------------------		
$("#nuovaRegistrazione").click(function() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			document.getElementById("nuovaRegistrazioneForm").reset();
			$("#codneg_cre").selectpicker('val', ' ');
			$("#causale_cre").selectpicker('val', ' ');
			$("#fornitore_cre").selectpicker('val', ' ');
			$("#cliente_cre").selectpicker('val', ' ');
			$("#scadenzesuppl_cre").html("");
			$("#dettagli_cre").html("");
			$("#dettagli_cre_messaggio").html("");			
			$("#nuova-registrazione-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaRegistrazioneFacade.class.php?modo=start", true);
	xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-nuova-scadenza-nuova-registrazione-form").click(function() {
	
	if ($("#fornitore_cre").val() != "") {
		$("#nuovaDataScadenzaCreazioneForm").attr("action", "../primanota/aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=go");
	}
	else if ($("#cliente_cre").val() != "") {
		$("#nuovaDataScadenzaCreazioneForm").attr("action", "../primanota/aggiungiNuovaScadenzaClienteFacade.class.php?modo=go");		
	}
	$("#nuova-data-scadenza-creazione-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuova-scadenza-modifica-registrazione-form").click(function() {
	
	if ($("#fornitore_mod").val() != "") {
		$("#nuovaDataScadenzaModificaForm").attr("action", "../primanota/aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=go");
	}
	else if ($("#cliente_mod").val() != "") {
		$("#nuovaDataScadenzaCreazioneForm").attr("action", "../primanota/aggiungiNuovaScadenzaClienteFacade.class.php?modo=go");
	}
	$("#nuova-data-scadenza-modifica-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuova-registrazione-form").click(function() {
	$("#nuovo-dettaglio-creazione-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-registrazione-form").click(function() {
	$("#nuovo-dettaglio-modifica-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuova-registrazione-form").click(
	function() {
		if (validaNuovaRegistrazione()) {
			$("#testo-messaggio-successo").html("Registrazione salvata con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#nuovaRegistrazioneForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore la registrazione non può essere salvata");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-registrazione-form").click(
	function() {
		if (validaModificaRegistrazione()) {
			$("#testo-messaggio-successo").html("Registrazione salvata con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#modificaRegistrazioneForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore la registrazione non può essere salvata");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovascad-nuova-registrazione-form").click(
	function() {				
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
		        		$("#scadenzesuppl_cre").html(xmlhttp.responseText);
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
		        		$("#scadenzesuppl_cre").html(xmlhttp.responseText);
		            }
		        }
		    } 
		    xmlhttp.open("GET", "aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&datascad_cli=" + datascad + "&cliente=" + cliente + "&impscad_cli=" + impscad + "&numfatt=" + numfatt, true);
		    xmlhttp.send();	
		} 
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-nuovascad-modifica-registrazione-form").click(
	function() {				
		var datascad = $("#newdatascad_mod").val();				
		var impscad  = $("#newimpscad_mod").val();
		var fornitore = $("#fornitore_mod").val();
		var cliente = $("#cliente_mod").val();
		var numfatt = $("#numfatt_mod").val();
		
		if (fornitore != " ")
		{	
			var xmlhttp = new XMLHttpRequest();
		    xmlhttp.onreadystatechange = function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		            if (xmlhttp.responseText != "") {
		        		$("#scadenzesuppl_mod").html(xmlhttp.responseText);
		            }
		        }				        
		    } 
		    xmlhttp.open("GET", "../primanota/aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore=" + fornitore + "&datascad_for=" + datascad + "&impscad_for=" + impscad + "&numfatt=" + numfatt, true);
		    xmlhttp.send();
		}
		else if (cliente != " ")
		{
			var xmlhttp = new XMLHttpRequest();
		    xmlhttp.onreadystatechange = function() {
		        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		            if (xmlhttp.responseText != "") { 
		        		$("#scadenzesuppl_mod").html(xmlhttp.responseText);
		            }
		        }
		    } 
		    xmlhttp.open("GET", "../primanota/aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&&cliente=" + cliente + "&datascad_cli=" + datascad + "&impscad_cli=" + impscad + "&numfatt=" + numfatt, true);
		    xmlhttp.send();	
		} 
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuova-registrazione-form").click(
	function() {

		var D_A = $("#newsegnodett_cre").val();
	
		// tolgo eventuali virgole nella descrizione del conto
		
		var conto = $("#conti").val().replace(",",".");
		var idconto = conto.substring(0, 6);
		
		// normalizzo la virgola dell'importo
		
		var importo = $("#newimpdett_cre").val();
		var importoNormalizzato = importo.trim().replace(",", ".");
	
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = 
			function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var sottocontiTable = xmlhttp.responseText;
					$("#dettagli_cre").html(sottocontiTable);
					controllaDettagliRegistrazione("dettagli_cre");
				}
			}
		xmlhttp.open("GET","aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto="	+ conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
		xmlhttp.send();
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-registrazione-form").click(
	function() {

		var D_A = $("#newsegnodett_mod").val();
	
		// tolgo eventuali virgole nella descrizione del conto
		
		var conto = $("#conti_mod").val().replace(",",".");
		var idconto = conto.substring(0, 6);
		
		// normalizzo la virgola dell'importo
		
		var importo = $("#newimpdett_mod").val();
		var importoNormalizzato = importo.trim().replace(",", ".");
	
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = 
			function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var sottocontiTable = xmlhttp.responseText;
					$("#dettagli_mod").html(sottocontiTable);
					controllaDettagliRegistrazione("dettagli_mod");
				}
			}
		xmlhttp.open("GET","../primanota/aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto="	+ conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
		xmlhttp.send();
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-cancella-registrazione-form").click(
	function() {
		$("#testo-messaggio-successo").html("Registrazione cancellata!");
		$("#messaggio-successo-dialog").modal("show");						
		sleep(3000);
		$("#cancellaRegistrazioneForm").submit();			
	}
);

//---------------------------------------------------------------------

function modificaRegistrazione(idRegistrazione)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			document.getElementById("modificaRegistrazioneForm").reset();

			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("registrazione").each(
				function() {

					$("#datareg_mod").val($(this).find("datareg").text());
					$("#descreg_mod").val($(this).find("descreg").text());

					var causale = $(this).find("causale").text();
					if (causale == "") causale = " ";
					$("#causale_mod").selectpicker('val',causale);
					
					var negozio = $(this).find("codneg").text();
					$("#codneg_mod").selectpicker('val', negozio);
					
					var fornitore = $(this).find("fornitore").text();
					var cliente   = $(this).find("cliente").text();
					if (fornitore == "") fornitore = " ";
					if (cliente == "") cliente = " ";
					
					$("#fornitore_mod").selectpicker('val',fornitore);
					$("#cliente_mod").selectpicker('val',cliente);
					$("#numfatt_mod").val($(this).find("numfatt").text());
					$("#numfatt_mod_orig").val($(this).find("numfattorig").text());
					
					if (fornitore != " ") $("#scadenzesuppl_mod").html($(this).find("scadenzesupplfornitore").text());
					if (cliente   != " ") $("#scadenzesuppl_mod").html($(this).find("scadenzesupplcliente").text());
					
					$("#dettagli_mod").html($(this).find("dettagli").text());
					$("#conti_mod").html($(this).find("conti").text());
				}
			)

			$("#modifica-registrazione-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","../primanota/modificaRegistrazioneFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------

function visualizzaRegistrazione(idRegistrazione)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("registrazione").each(
				function() {

					$("#datareg_vis").html($(this).find("datareg").text());
					$("#descreg_vis").html($(this).find("descreg").text());
					$("#causale_vis").html($(this).find("causale").text());
					$("#codneg_vis").html($(this).find("codneg").text());
					
					var fornitore = $(this).find("fornitore").text();
					var cliente   = $(this).find("cliente").text();

					$("#fornitore_vis").html(fornitore);
					$("#cliente_vis").html(cliente);
					
					$("#numfatt_vis").html($(this).find("numfatt").text());
					
					if (fornitore != "") {
						$("#scadenzesuppl_vis").html($(this).find("scadenzesupplfornitore").text());
						$("#cliente_vis_label").hide();
					}
					if (cliente   != "") {
						$("#scadenzesuppl_vis").html($(this).find("scadenzesupplcliente").text());
						$("#fornitore_vis_label").hide();
					}
					
					$("#dettagli_vis").html($(this).find("dettagli").text());
				}
			)

			$("#visualizza-registrazione-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
	xmlhttp.send();
}

// ---------------------------------------------------------------------------------
// CREA REGISTRAZIONE : routine di validazione
// ---------------------------------------------------------------------------------

function validaNuovaRegistrazione() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1) Se la validazione è positiva viene abilitato il bottone
	 * ok di conferma inserimento
	 */
	var esito = "";

	controllaDataRegistrazione("datareg_cre");
	if ($("#datareg_cre_messaggio").text() == "") esito = esito + "1";
	else esito = esito + "0";

	if ($("#descreg_cre").val() != "") {
		if (controllaDescrizione("descreg_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}
	
	if ($("#causale_cre").val() != "") {
		controllaDettagliRegistrazione("dettagli_cre");
		if ($("#dettagli_cre_messaggio").text() == "") esito = esito + "1";
		else esito = esito + "0";
	}

	if (controllaCausale("causale_cre")) { esito = esito + "1"; }
	else { esito = esito + "0"; }

	if (controllaClienteFornitore("fornitore_cre", "cliente_cre")) { esito = esito + "1"; }
	else { esito = esito + "0"; }

	if (($("#fornitore_cre").val() != "") || $("#cliente_cre").val() != "") {
		if (controllaNumeroFattura("numfatt_cre")) esito = esito + "1";
		else esito = esito + "0";
	}
		
	if ($("#fornitore_cre").val() != "") {
		controllaNumeroFatturaFornitore("fornitore_cre", "numfatt_cre", "datareg_cre");
		if ($("#fornitore_cre_messaggio").text() == "") esito = esito + "1";
		else esito = esito + "0";
	}
	else if ($("#cliente_cre").val() != "") {
		controllaNumeroFatturaCliente("cliente_cre", "numfatt_cre", "datareg_cre");
		if ($("#cliente_cre_messaggio").text() == "") esito = esito + "1";
		else esito = esito + "0";
	}

	if (esito == "1111111") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------------------
// MODIFICA REGISTRAZIONE : routine di validazione
//---------------------------------------------------------------------------------

function validaModificaRegistrazione()
{
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1) Se la validazione è positiva viene abilitato il bottone
	 * ok di conferma inserimento
	 */
	var esito = "";

	controllaDataRegistrazione("datareg_mod");
	if ($("#datareg_mod_messaggio").text() == "") esito = esito + "1";
	else esito = esito + "0";

	if ($("#descreg_mod").val() != "") {
		if (controllaDescrizione("descreg_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}
	
	if ($("#causale_mod").val() != "") {
		controllaDettagliRegistrazione("dettagli_mod");
		if ($("#dettagli_mod_messaggio").text() == "") esito = esito + "1";
		else esito = esito + "0";
	}

	if (controllaCausale("causale_mod")) { esito = esito + "1"; }
	else { esito = esito + "0"; }

	if (controllaClienteFornitore("fornitore_mod", "cliente_mod")) { esito = esito + "1"; }
	else { esito = esito + "0"; }

	if (($("#fornitore_mod").val() != "") || $("#cliente_mod").val() != "") {
		if (controllaNumeroFattura("numfatt_mod")) esito = esito + "1";
		else esito = esito + "0";
	}
		
	if ($("#fornitore_mod").val() != "") {
		controllaNumeroFatturaFornitore("fornitore_mod", "numfatt_mod", "datareg_mod");
		if ($("#fornitore_mod_messaggio").text() == "") esito = esito + "1";
		else esito = esito + "0";
	}
	else if ($("#cliente_mod").val() != "") {
		controllaNumeroFatturaCliente("cliente_mod", "numfatt_mod", "datareg_mod");
		if ($("#cliente_mod_messaggio").text() == "") esito = esito + "1";
		else esito = esito + "0";
	}

	if (esito == "1111111") { return true; }
	else { return false; }
}

// ---------------------------------------------------------------------
// Function di aggiornamento
// ---------------------------------------------------------------------

function modificaImportoDettaglioRegistrazione(idTable,conto,sottoconto,importo,idDettaglio)
{
	if (importo == "") var importoDett = 0;
	else var importoDett = importo;
	
	 if (conto != "") {
		 var xmlhttp = new XMLHttpRequest();
		 xmlhttp.onreadystatechange = function() {
			 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				 if (xmlhttp.responseText != "") {
					 var dettagliTable = xmlhttp.responseText;
					 $("#" + idTable).html(dettagliTable); // modifica registrazione
					
					 var imp = $("#importo" + conto + sottoconto).val();
					 $("#importo" + conto + sottoconto).focus().val('').val(imp);

					 controllaDettagliRegistrazione(idTable);
				 }
			 }
		 }
		 xmlhttp.open("GET","../primanota/aggiornaImportoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&importo=" + importoDett + "&iddettaglio=" + idDettaglio, true);
		 xmlhttp.send();
	 }
 }

//---------------------------------------------------------------------

function modificaSegnoDettaglioRegistrazione(idTable,conto,sottoconto,segno,idDettaglio)
{
	if (conto != "") {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					var dettagliTable = xmlhttp.responseText;
					$("#" + idTable).html(dettagliTable);
	
					var segno = $("#segno" + conto + sottoconto).val();
					$("#segno" + conto + sottoconto).focus().val('').val(segno);
	        		
					controllaDettagliRegistrazione(idTable);
				}
			}
		}
		xmlhttp.open("GET", "../primanota/aggiornaSegnoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&dareAvere=" + segno + "&iddettaglio=" + idDettaglio, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------

function aggiungiDettaglioContoFornitore(idfornitore, campoDett)
{
	if (idfornitore != "")
	{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					var dettagliTable = xmlhttp.responseText;
					$("#" + campoDett).html(dettagliTable);
					$("#" + campoDett).show();
					controllaDettagliRegistrazione(campoDett);
				}
			}
		}
		xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioContoFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------

function aggiungiDettaglioContoCliente(idcliente, campoDett)
{
	if (idcliente != "")
	{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					var dettagliTable = xmlhttp.responseText;
					$("#" + campoDett).html(dettagliTable);
					$("#" + campoDett).show();
					controllaDettagliRegistrazione(campoDett);
				}
			}
		}
		xmlhttp.open("GET","../primanota/aggiungiNuovoDettaglioContoClienteFacade.class.php?modo=go&idcliente=" + idcliente, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------------------

function cancellaNuovaScadenzaFornitore(idTable, idFornitore, datScad, numFatt)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			$("#" + idTable).html(xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET", "../primanota/cancellaScadenzaFornitoreFacade.class.php?modo=start&idfornitore=" + idFornitore + "&datascad_for=" + datScad + "&numfatt=" + numFatt, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function cancellaNuovaScadenzaCliente(idTable, idCliente, datScad, numFatt)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			$("#" + idTable).html(xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET", "../primanota/cancellaScadenzaClienteFacade.class.php?modo=start&idcliente=" + idCliente + "&datascad_cli=" + datScad + "&numfatt=" + numFatt, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function modificaImportoScadenzaFornitore(idTable, idfornitore, datascad, numfatt, importo)
{
	if (importo == "") var importoScad = 0;
	else var importoScad = importo;

	if (datascad != "") {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					var scadenzeTable = xmlhttp.responseText;
					$("#" + idTable).html(scadenzeTable);

					var imp = $("#impscad" + idfornitore + datascad + numfatt).val();
					$("#impscad" + idfornitore + datascad + numfatt).focus().val('').val(imp);
				}
			}
		}
		xmlhttp.open("GET","../primanota/aggiornaImportoScadenzaFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore + "&datascad_for=" + datascad + "&numfatt=" + numfatt + "&impscad_for=" + importoScad, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------------------

function modificaImportoScadenzaCliente(idTable, idcliente, datascad, numfatt, importo)
{
	if (importo == "") var importoScad = 0;
	else var importoScad = importo;

	if (datascad != "") {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					var scadenzeTable = xmlhttp.responseText;
					$("#" + idTable).html(scadenzeTable);

					var imp = $("#impscad" + idcliente + datascad + numfatt).val();
					$("#impscad" + idcliente + datascad + numfatt).focus().val('').val(imp);
				}
			}
		}
		xmlhttp.open("GET", "../primanota/aggiornaImportoScadenzaClienteFacade.class.php?modo=go&idcliente=" + idcliente + "&datascad_cli=" + datascad + "&numfatt=" + numfatt + "&impscad_cli=" + importoScad, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------------------

function cancellaDettaglioNuovaRegistrazione(idTable,codContoComposto)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var dettagliTable = xmlhttp.responseText;
			$("#" + idTable).html(dettagliTable);
			controllaDettagliRegistrazione(idTable);
		}
	}
	xmlhttp.open("GET","cancellaNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + codContoComposto, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function cancellaRegistrazione(idreg) {
	$("#idreg").val(idreg);
	$("#cancella-registrazione-dialog").modal("show");
}

// ---------------------------------------------------------------------------------
// Funzioni utility
// ---------------------------------------------------------------------------------

$("#fornitore_cre").change(
	function() {

		var idfornitore = $("#fornitore_cre").val();
		var datareg = $("#datareg_cre").val();

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#datascad_cre_label").show();
					$("#scadenzesuppl_cre").html(xmlhttp.responseText);
					controllaNumeroFattura("numfatt_cre");
				}
			}
		}
		xmlhttp.open("GET","../primanota/calcolaDataScadenzaFornitoreFacade.class.php?modo=start&idfornitore="+ idfornitore + "&datareg=" + datareg, true);
		xmlhttp.send();
	}
);

//---------------------------------------------------------------------------------

$("#fornitore_mod").change(
	function() {

		var desfornitore = $("#fornitore_mod").val();
		var datareg = $("#datareg_mod").val();

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#scadenzesuppl_mod").html(xmlhttp.responseText);
					controllaNumeroFattura("numfatt_mod");
				}
			}
		}
		xmlhttp.open("GET","../primanota/calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore="+ desfornitore + "&datareg=" + datareg, true);
		xmlhttp.send();
	}
);

// ---------------------------------------------------------------------------------

$("#cliente_cre").change(
	function() {

		var descliente = $("#cliente_cre").val();
		var datareg = $("#datareg_cre").val();

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#datascad_cre_label").show();
					$("#scadenzesuppl_cre").html(xmlhttp.responseText);
					controllaNumeroFattura("numfatt_cre");
				}
			}
		}
		xmlhttp.open("GET","../primanota/calcolaDataScadenzaClienteFacade.class.php?modo=start&descliente=" + descliente + "&datareg=" + datareg, true);
		xmlhttp.send();
	}
);

//---------------------------------------------------------------------------------

$("#cliente_mod").change(
	function() {

		var descliente = $("#cliente_mod").val();
		var datareg = $("#datareg_mod").val();

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#scadenzesuppl_mod").html(xmlhttp.responseText);
					controllaNumeroFattura("numfatt_mod");
				}
			}
		}
		xmlhttp.open("GET","../primanota/calcolaDataScadenzaClienteFacade.class.php?modo=start&descliente=" + descliente + "&datareg=" + datareg, true);
		xmlhttp.send();
	}
);

// ---------------------------------------------------------------------------------

$("#causale_cre").change(
	function() {
		var causale = $("#causale_cre").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti").html(xmlhttp.responseText);
						$('#conti').selectpicker('refresh');
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_mod").change(
	function() {
		var causale = $("#causale_mod").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_mod").html(xmlhttp.responseText);
						$("#conti_mod").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_inc").change(
	function() {
		var causale = $("#causale_inc").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_inc").html(xmlhttp.responseText);
						$("#conti_inc").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_inc_mod").change(
	function() {
		var causale = $("#causale_inc_mod").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_inc_mod").html(xmlhttp.responseText);
						$("#conti_inc_mod").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_pag_cre").change(
	function() {
		var causale = $("#causale_pag_cre").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_pag").html(xmlhttp.responseText);
						$("#conti_pag").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_pag_mod").change(
	function() {
		var causale = $("#causale_pag_mod").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_pag_mod").html(xmlhttp.responseText);
						$("#conti_pag_mod").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_cormer_cre").change(
	function() {
		var causale = $("#causale_cormer_cre").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_cormer_cre").html(xmlhttp.responseText);
						$("#conti_cormer_cre").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_cormer_mod").change(
	function() {
		var causale = $("#causale_cormer_mod").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_cormer_mod").html(xmlhttp.responseText);
						$("#conti_cormer_mod").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_corneg_cre").change(
	function() {
		var causale = $("#causale_corneg_cre").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_corneg_cre").html(xmlhttp.responseText);
						$("#conti_corneg_cre").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

//---------------------------------------------------------------------------------

$("#causale_corneg_mod").change(
	function() {
		var causale = $("#causale_corneg_mod").val();

		if (causale != "") {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = 
				function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						$("#conti_corneg_mod").html(xmlhttp.responseText);
						$("#conti_corneg_mod").selectpicker("refresh");
					}
				}
			xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();
		}
	}
);

// ---------------------------------------------------------------------------------