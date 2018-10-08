//---------------------------------------------------------------------------------				
// Fornitori
//---------------------------------------------------------------------------------				

$("#nuovoFornitore").click(function() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			document.getElementById("nuovoFornitoreForm").reset();
            if (xmlhttp.responseText != "") {
        		$("#codforn_cre").val(xmlhttp.responseText);
        		$("#ggscadfat_cre").val(30);        		
            }			
			$("#nuovo-fornitore-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaFornitoreFacade.class.php?modo=start", true);
	xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-fornitore-form").click(
	function() {
		if (validaNuovoFornitore()) {
			$("#testo-messaggio-successo").html("Fornitore salvato con successo, conto fornitore creato!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(2000);
			$("#nuovoFornitoreForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il fornitore non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-fornitore-form").click(
	function() {
		if (validaModificaFornitore()) {
			$("#testo-messaggio-successo").html("Fornitore salvato con successo");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(2000);
			$("#modificaFornitoreForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il fornitore non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

function cancellaFornitore(idfornitore) {
	$("#idfornitore").val(idfornitore);
	$("#cancella-fornitore-dialog").modal("show");
}

//---------------------------------------------------------------------------------

$("#button-ok-cancella-fornitore-form").click(
	function() {
		$("#testo-messaggio-successo").html("Fornitore cancellato!");
		$("#messaggio-successo-dialog").modal("show");						
		sleep(2000);
		$("#cancellaFornitoreForm").submit();			
	}
);

//---------------------------------------------------------------------

function modificaFornitore(idFornitore)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			document.getElementById("modificaFornitoreForm").reset();

			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("fornitore").each(
				function() {

					$("#codforn_mod").val($(this).find("codice").text());
					$("#desforn_mod").val($(this).find("descrizione").text());
					$("#indforn_mod").val($(this).find("indirizzo").text());
					$("#cittaforn_mod").val($(this).find("citta").text());
					$("#capforn_mod").val($(this).find("cap").text());
					
					$("#bonifico_mod").parent('.btn').removeClass('active');
					$("#riba_mod").parent('.btn').removeClass('active');
					$("#rimdiretta_mod").parent('.btn').removeClass('active');
					$("#assegnobancario_mod").parent('.btn').removeClass('active');
					$("#addebitodiretto_mod").parent('.btn').removeClass('active');

					var tipoAddebito = $(this).find("tipoAddebito").text();
					
					if (tipoAddebito == "BONIFICO") {
						$("#bonifico_mod").parent('.btn').addClass('active');
						$("#bonifico_mod").prop('checked',true);
					}
					if (tipoAddebito == "RIBA") {
						$("#riba_mod").parent('.btn').addClass('active');
						$("#riba_mod").prop('checked',true);
					}
					if (tipoAddebito == "RIM_DIR") {
						$("#rimdiretta_mod").parent('.btn').addClass('active');
						$("#rimdiretta_mod").prop('checked',true);
					}
					if (tipoAddebito == "ASS_BAN") {
						$("#assegnobancario_mod").parent('.btn').addClass('active');
						$("#assegnobancario_mod").prop('checked',true);
					}
					if (tipoAddebito == "ADD_DIR") {
						$("#addebitodiretto_mod").parent('.btn').addClass('active');
						$("#addebitodiretto_mod").prop('checked',true);
					}
					
					$("#ggscadfat_mod").val($(this).find("giorniScadenzaFattura").text());					
				}
			)

			$("#modifica-fornitore-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","modificaFornitoreFacade.class.php?modo=start&idfornitore=" + idFornitore, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------
// CREA FORNITORE : routine di validazione
//---------------------------------------------------------------------------------

function validaNuovoFornitore() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codforn_cre").val() != "") {
		if (controllaCodice("codforn_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#desforn_cre").val() != "") {
		if (controllaDescrizione("desforn_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#ggscadfat_cre").val() != "") {
		if (controllaQuantita("ggscadfat_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if (esito == "111") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------------------
//MODIFICA FORNITORE : routine di validazione
//---------------------------------------------------------------------------------

function validaModificaFornitore() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#desforn_mod").val() != "") {
		if (controllaDescrizione("desforn_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#ggscadfat_mod").val() != "") {
		if (controllaQuantita("ggscadfat_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if (esito == "11") { return true; }
	else { return false; }
}
