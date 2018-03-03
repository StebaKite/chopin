//---------------------------------------------------------------------------------				
// Scadenze
//---------------------------------------------------------------------------------

function visualizzaScadenzaFornitore(idScadenza)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("scadenza").each(
				function() {

					$("#datascad_vis").html($(this).find("data").text());
					$("#notascad_vis").html($(this).find("nota").text());
					$("#impscad_vis").html($(this).find("importo").text());

					$("#tipaddeb_vis").html($(this).find("addebito").text());
					$("#stascad_vis").html($(this).find("stato").text());
					$("#fatscad_vis").html($(this).find("fattura").text());
					
					$("#regOrigTable_vis").html($(this).find("registrazioneOriginante").text());
					$("#pagamentoTable_vis").html($(this).find("pagamento").text());
				}
			)
			$("#visualizza-scadenza-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","../scadenze/visualizzaScadenzaFornitoreFacade.class.php?modo=start&idScadenza=" + idScadenza, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------

function modificaScadenzaFornitore(idScadenza)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("scadenza").each(
				function() {

					$("#datascad_mod").val($(this).find("data").text());
					$("#notascad_mod").val($(this).find("nota").text());
					$("#impscad_mod").val($(this).find("importo").text());

					$("#tipaddeb_mod").html($(this).find("addebito").text());
					$("#stascad_mod").html($(this).find("stato").text());
					$("#fatscad_mod").val($(this).find("fattura").text());
					
					$("#regOrigTable_mod").html($(this).find("registrazioneOriginante").text());
					$("#pagamentoTable_mod").html($(this).find("pagamento").text());
				}
			)
			$("#modifica-scadenza-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","../scadenze/modificaScadenzaFornitoreFacade.class.php?modo=start&idScadenza=" + idScadenza, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------
