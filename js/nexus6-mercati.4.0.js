//---------------------------------------------------------------------------------				
// Mercati
//---------------------------------------------------------------------------------				

$("#nuovoMercato").click(function() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			document.getElementById("nuovoMercatoForm").reset();
			$("#nuovo-mercato-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaMercatoFacade.class.php?modo=start", true);
	xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-mercato-form").click(
	function() {
		if (validaNuovoMercato()) {
			$("#testo-messaggio-successo").html("Mercato salvato con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(2000);
			$("#nuovoMercatoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il mercato non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-mercato-form").click(
	function() {
		if (validaModificaMercato()) {
			$("#testo-messaggio-successo").html("Mercato salvato con successo");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(2000);
			$("#modificaMercatoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il mercato non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------

function modificaMercato(idMercato)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			document.getElementById("modificaMercatoForm").reset();

			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("mercato").each(
				function() {

					$("#codmer_mod").val($(this).find("codice").text());
					$("#desmer_mod").val($(this).find("descrizione").text());
					$("#citmer_mod").val($(this).find("citta").text());

					var negozio = $(this).find("negozio").text();
					$("#negmer_mod").selectpicker('val',negozio);
				}
			)

			$("#modifica-mercato-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","modificaMercatoFacade.class.php?modo=start&idmercato=" + idMercato, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function cancellaMercato(idmercato)
{
	$("#idmercato").val(idmercato);
	$("#cancella-mercato-dialog").modal("show");
}

//---------------------------------------------------------------------------------

$("#button-ok-cancella-mercato-form").click(
	function() {
		$("#testo-messaggio-successo").html("Mercato cancellato!");
		$("#messaggio-successo-dialog").modal("show");						
		sleep(2000);
		$("#cancellaMercatoForm").submit();			
	}
);

//---------------------------------------------------------------------------------
//CREA MERCATO : routine di validazione
//---------------------------------------------------------------------------------

function validaNuovoMercato() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codmer_cre").val() != "") {
		if (controllaCodice("codmer_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#desmer_cre").val() != "") {
		if (controllaDescrizione("desmer_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#citmer_cre").val() != "") {
		if (controllaDescrizione("citmer_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if (esito == "111") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------------------
//MODIFICA MERCATO : routine di validazione
//---------------------------------------------------------------------------------

function validaModificaMercato() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codmer_mod").val() != "") {
		if (controllaCodice("codmer_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#desmer_mod").val() != "") {
		if (controllaDescrizione("desmer_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#citmer_mod").val() != "") {
		if (controllaDescrizione("citmer_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if (esito == "111") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------------------
