//---------------------------------------------------------------------------------				
// Causali
//---------------------------------------------------------------------------------				

$("#nuovaCausale").click(function() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			$("#codcausale_cre").val("");	
			$("#codcausale_cre").parent('.input-group').removeClass('has-error');
			$("#descausale_cre").val("");	
			$("#descausale_cre").parent('.input-group').removeClass('has-error');
			
			$("#nuova-causale-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaCausaleFacade.class.php?modo=start", true);
	xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-ok-nuova-causale-form").click(
	function() {
		if (validaCausale("cre")) {
			$("#testo-messaggio-successo").html("Causale salvata con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#nuovaCausaleForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore la causale non può essere salvata");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------
// CREA CAUSALE : routine di validazione
//---------------------------------------------------------------------------------

function validaCausale(funz) {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codcausale_" + funz).val() != "") {
		if (controllaCodice("codcausale_" + funz)) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#descausale_" + funz).val() != "") {
		if (controllaDescrizione("descausale_" + funz)) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}
	
	if (esito == "11") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------

function modificaCausale(codCausale)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("causale").each(
				function() {

					$("#codcausale_mod").val($(this).find("codice").text());
					$("#descausale_mod").val($(this).find("descrizione").text());

					$("#catcausale_generi_mod").parent('.btn').removeClass('active');
					$("#catcausale_incpag_mod").parent('.btn').removeClass('active');
					$("#catcausale_corris_mod").parent('.btn').removeClass('active');

					var categoria = $(this).find("categoria").text();

					//----------------------------------------------------------
					if (categoria == "GENERI") {
						$("#catcausale_generi_mod").parent('.btn').addClass('active');
						$("#catcausale_generi_mod").prop('checked',true);
					}
					if (categoria == "INCPAG") {
						$("#catcausale_incpag_mod").parent('.btn').addClass('active');
						$("#catcausale_incpag_mod").prop('checked',true);
					}
					if (categoria == "CORRIS") {
						$("#catcausale_corris_mod").parent('.btn').addClass('active');
						$("#catcausale_corris_mod").prop('checked',true);
					}
					
					//----------------------------------------------------------
					
					$("#sottocontiTable_mod").html($(this).find("sottoconti").text());
				}
			)

			$("#modifica-causale-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","modificaCausaleFacade.class.php?modo=start&codcausale_mod=" + codCausale, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#button-ok-modifica-causale-form").click(
	function() {
		if (validaCausale("mod")) {
			$("#testo-messaggio-successo").html("Causale salvata con successo");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#modificaCausaleForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore la causale non può essere salvata");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

function configuraCausale(codCausale)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("causale").each(
				function() {
					$("#conti_configurati_causale_mod").html($(this).find("conticonfigurati").text());
					$("#conti_disponibili_causale_mod").html($(this).find("contidisponibili").text());
				}
			)
		}      	
		$("#configura-causale-dialog").modal("show");
	}
	xmlhttp.open("GET", "configuraCausaleFacade.class.php?modo=start&codcausale_conf=" + codCausale, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------			

function includiConto(codConto)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("causale").each(
				function() {
					$("#conti_configurati_causale_mod").html($(this).find("conticonfigurati").text());
					$("#conti_disponibili_causale_mod").html($(this).find("contidisponibili").text());
				}
			)
		}
	}
	xmlhttp.open("GET", "includiContoCausaleFacade.class.php?modo=start&codconto_conf=" + codConto, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------			

function escludiConto(codConto)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("causale").each(
				function() {
					$("#conti_configurati_causale_mod").html($(this).find("conticonfigurati").text());
					$("#conti_disponibili_causale_mod").html($(this).find("contidisponibili").text());
				}
			)
		}
	}
	xmlhttp.open("GET", "escludiContoCausaleFacade.class.php?modo=start&codconto_conf=" + codConto, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function cancellaCausale(codcausale)
{
	$("#codcausale_del").val(codcausale);
	$("#cancella-causale-dialog").modal("show");
}

//---------------------------------------------------------------------------------

$("#button-ok-cancella-causale-form").click(
	function() {
		$("#testo-messaggio-successo").html("Ho cancellato la causale");
		$("#messaggio-successo-dialog").modal("show");						
		sleep(3000);
		$("#cancellaCausaleForm").submit();			
	}
);

//---------------------------------------------------------------------------------
