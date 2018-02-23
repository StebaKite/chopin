//---------------------------------------------------------------------------------				
// Progressivi
//---------------------------------------------------------------------------------				

function modificaProgressivoFattura(catCliente, codNegozio) {
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("progressivo").each(
				function() {

					$("#catcliente_mod").val($(this).find("categoria").text());
					$("#codnegozio_mod").val($(this).find("negozio").text());
					$("#numfatt_mod").val($(this).find("numfatturaultimo").text());
					$("#notatesta_mod").val($(this).find("notatestata").text());
					$("#notapiede_mod").val($(this).find("notapiede").text());
				}
			)
			$("#modifica-progressivo-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "modificaProgressivoFatturaFacade.class.php?modo=start&catcliente_mod=" + catCliente + "&codnegozio_mod=" + codNegozio, true);
    xmlhttp.send();				
}

//---------------------------------------------------------------------------------				

$("#button-ok-modifica-progressivo-form").click(
	function() {
		if (validaProgressivo("mod")) {
			$("#testo-messaggio-successo").html("Progressivo fattura salvato con successo");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#modificaProgressivoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il progressivo non può essere salvata");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------
// VALIDAZIONE FORM CAUSALE
//---------------------------------------------------------------------------------

function validaProgressivo(funz) {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#catcliente_" + funz).val() != "") {
		if (controllaCodice("catcliente_" + funz)) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#codnegozio_" + funz).val() != "") {
		if (controllaCodice("codnegozio_" + funz)) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#numfatt_" + funz).val() != "") {
		if (controllaNumero("numfatt_" + funz)) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if (esito == "111") { return true; }
	else { return false; }
}

