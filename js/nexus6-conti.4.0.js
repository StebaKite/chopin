//---------------------------------------------------------------------------------				
// Conti e Sottoconti
//---------------------------------------------------------------------------------

$("#nuovoConto").click(function() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			$("#codconto_cre").val("");	
			$("#codconto_cre").parent('.input-group').removeClass('has-error');
			$("#desconto_cre").val("");	
			$("#desconto_cre").parent('.input-group').removeClass('has-error');
			$("#numrigabilancio_cre").val("");	
			$("#numrigabilancio_cre").parent('.input-group').removeClass('has-error');
			$("#sottocontiTable_cre").html("");
			
			$("#nuovo-conto-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaContoFacade.class.php?modo=start", true);
	xmlhttp.send();
});


//---------------------------------------------------------------------------------

$("#button-nuovo-sottoconto-nuovo-conto-form").click(function() {

	var codconto = $("#codconto_cre").val();
	var desconto = $("#desconto_cre").val();
	var numrigabilancio = $("#numrigabilancio_cre").val();

	$("#desconto_cre_control_group").removeClass("has-error");
	
	if (codconto == "") $("#codconto_cre_control_group").addClass("has-error");
	if (desconto == "") $("#desconto_cre_control_group").addClass("has-error");
	if (!controllaNumero("numrigabilancio_cre")) $("#numrigabilancio_cre_control_group").addClass("has-error");
	
	if ((codconto != "") && (desconto != "") && (numrigabilancio != ""))  {
		if (!$("#codconto_cre_control_group").hasClass("has-error") && !$("#numrigabilancio_cre_control_group").hasClass("has-error"))
		$("#nuovo-sottoconto-nuovo-conto-dialog").modal("show");
	}
});

//---------------------------------------------------------------------------------

$("#button-nuovo-sottoconto-modifica-conto-form").click(function() {

	var codconto = $("#codconto_mod").val();
	var desconto = $("#desconto_mod").val();
	var numrigabilancio = $("#numrigabilancio_mod").val();

	$("#desconto_mod_control_group").removeClass("has-error");
	
	if (codconto == "") $("#codconto_mod_control_group").addClass("has-error");
	if (desconto == "") $("#desconto_mod_control_group").addClass("has-error");
	if (!controllaNumero("numrigabilancio_mod")) $("#numrigabilancio_mod_control_group").addClass("has-error");
	
	if ((codconto != "") && (desconto != "") && (numrigabilancio != ""))  {
		if (!$("#codconto_mod_control_group").hasClass("has-error") && !$("#numrigabilancio_mod_control_group").hasClass("has-error"))
		$("#nuovo-sottoconto-modifica-conto-dialog").modal("show");
	}
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-sottoconto-nuovo-conto-form").click(function() {

		var codconto = $("#codconto_cre").val();
		var codsottoconto = $("#codsottoconto_cre").val();
		var dessottoconto = $("#dessottoconto_cre").val();
		
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        		$("#sottocontiTable_cre").html(xmlhttp.responseText);
	        }
	    }
	    xmlhttp.open("GET", "aggiungiNuovoSottocontoFacade.class.php?modo=start&codsottoconto=" + codsottoconto + "&dessottoconto=" + dessottoconto + "&codconto=" + codconto, true);
	    xmlhttp.send();		
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-sottoconto-modifica-conto-form").click(function() {

		var codconto = $("#codconto_mod").val();
		var codsottoconto = $("#codsottoconto_mod").val();
		var dessottoconto = $("#dessottoconto_mod").val();
		
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      		$("#sottocontiTable_mod").html(xmlhttp.responseText);
	        }
	    }
	    xmlhttp.open("GET", "aggiungiNuovoSottocontoFacade.class.php?modo=start&codsottoconto=" + codsottoconto + "&dessottoconto=" + dessottoconto + "&codconto=" + codconto, true);
	    xmlhttp.send();		
	}
);		

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-conto-form").click(
	function() {
		if (validaNuovoConto()) {
			$("#testo-messaggio-successo").html("Conto salvato con successo!");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#nuovoContoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il conto non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-conto-form").click(
	function() {
		if (validaModificaConto()) {
			$("#testo-messaggio-successo").html("Conto salvato con successo");
			$("#messaggio-successo-dialog").modal("show");						
			sleep(3000);
			$("#modificaContoForm").submit();			
		}
		else {
			$("#testo-messaggio-errore").html("In presenza di campi in errore il conto non può essere salvato");
			$("#messaggio-errore-dialog").modal("show");			
		}
	}
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-gruppo-sottoconto-form").click(function() {

		var codconto = $("#codconto_modgru").val();
		var codsottoconto = $("#codsottoconto_modgru").val();
		if ($("#indgruppoNS_modgru").parent(".btn").hasClass("active")) var indgruppo = $("#indgruppoNS_modgru").val();		
		if ($("#indgruppoCF_modgru").parent(".btn").hasClass("active")) var indgruppo = $("#indgruppoCF_modgru").val();
		if ($("#indgruppoCV_modgru").parent(".btn").hasClass("active")) var indgruppo = $("#indgruppoCV_modgru").val();
		if ($("#indgruppoRC_modgru").parent(".btn").hasClass("active")) var indgruppo = $("#indgruppoRC_modgru").val();
		
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      		$("#sottocontiTable_mod").html(xmlhttp.responseText);
	        }
	    }
	    xmlhttp.open("GET", "modificaGruppoSottocontoFacade.class.php?modo=start&codsottoconto_modgru=" + codsottoconto + "&indgruppo_modgru=" + indgruppo + "&codconto_modgru=" + codconto, true);
	    xmlhttp.send();		
	}
);		

//---------------------------------------------------------------------------------				

function cancellaSottoconto(codsottoconto, codconto, funzione)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			$("#sottocontiTable" + funzione).html(xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET", "togliNuovoSottocontoFacade.class.php?modo=start&codsottoconto_del=" + codsottoconto + "&codconto_del=" + codconto, true);
	xmlhttp.send();				
}

//---------------------------------------------------------------------------------

function cancellaConto(codconto)
{
	$("#codconto").val(codconto);
	$("#cancella-conto-dialog").modal("show");
}

//---------------------------------------------------------------------------------

$("#button-ok-cancella-conto-form").click(
	function() {
		$("#testo-messaggio-successo").html("Ho cancellato il conto e tutti i suoi sottoconti");
		$("#messaggio-successo-dialog").modal("show");						
		sleep(3000);
		$("#cancellaContoForm").submit();			
	}
);

//---------------------------------------------------------------------

function modificaConto(codConto)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
		{
			var parser = new DOMParser();
			var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");
			
			$(xmldoc).find("conto").each(
				function() {

					$("#codconto_mod").val($(this).find("codice").text());
					$("#desconto_mod").val($(this).find("descrizione").text());
					$("#numrigabilancio_mod").val($(this).find("numeroRigaBilancio").text());

					$("#catconto_mod").parent('.btn').removeClass('active');
					$("#dareavere_mod").parent('.btn').removeClass('active');
					$("#indpresenza_mod").parent('.btn').removeClass('active');
					$("#indvissottoconti_mod").parent('.btn').removeClass('active');

					var categoria = $(this).find("categoria").text();
					var tipo = $(this).find("tipo").text();
					var presenzaInBil = $(this).find("presenzaInBilancio").text();
					var presenzaSottoconti = $(this).find("presenzaSottoconti").text();

					//----------------------------------------------------------
					if (categoria == "Conto Economico") {
						$("#contoeco_mod").parent('.btn').addClass('active');
						$("#contoeco_mod").prop('checked',true);
					}
					if (categoria == "Stato Patrimoniale") {
						$("#contopat_mod").parent('.btn').addClass('active');
						$("#contopat_mod").prop('checked',true);
					}
					//----------------------------------------------------------
					if (tipo == "Dare") {
						$("#dare_mod").parent('.btn').addClass('active');
						$("#dare_mod").prop('checked',true);
					}
					if (tipo == "Avere") {
						$("#avere_mod").parent('.btn').addClass('active');
						$("#avere_mod").prop('checked',true);
					}
					//----------------------------------------------------------
					if (presenzaInBil == "S") {
						$("#presenzaSi_mod").parent('.btn').addClass('active');
						$("#presenzaSi_mod").prop('checked',true);
					}
					if (presenzaInBil == "N") {
						$("#presenzaNo_mod").parent('.btn').addClass('active');
						$("#presenzaNo_mod").prop('checked',true);
					}
					//----------------------------------------------------------
					if (presenzaSottoconti == "S") {
						$("#sottocontiSi_mod").parent('.btn').addClass('active');
						$("#sottocontiSi_mod").prop('checked',true);
					}
					if (presenzaSottoconti == "N") {
						$("#sottocontiNo_mod").parent('.btn').addClass('active');
						$("#sottocontiNo_mod").prop('checked',true);
					}
					
					//----------------------------------------------------------
					
					$("#sottocontiTable_mod").html($(this).find("sottoconti").text());
				}
			)

			$("#modifica-conto-dialog").modal("show");
		}
	}
	xmlhttp.open("GET","modificaContoFacade.class.php?modo=start&codconto=" + codConto, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------
// CREA CONTO : routine di validazione
//---------------------------------------------------------------------------------

function validaNuovoConto() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codconto_cre").val() != "") {
		if (controllaCodice("codconto_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#desconto_cre").val() != "") {
		if (controllaDescrizione("desconto_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#numrigabilancio_cre").val() != "") {
		if (controllaNumero("numrigabilancio_cre")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}
	
	if (esito == "111") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------------------
// MODIFICA CONTO : routine di validazione
//---------------------------------------------------------------------------------

function validaModificaConto() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1)
	 */
	var esito = "";

	if ($("#codconto_mod").val() != "") {
		if (controllaCodice("codconto_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#desconto_mod").val() != "") {
		if (controllaDescrizione("desconto_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}

	if ($("#numrigabilancio_mod").val() != "") {
		if (controllaNumero("numrigabilancio_mod")) { esito = esito + "1"; }
		else { esito = esito + "0"; }
	}
	
	if (esito == "111") { return true; }
	else { return false; }
}

//---------------------------------------------------------------------

function controllaConto(campoCodConto)
{	
	$("#" + campoCodConto + "_control_group").removeClass("has-error");

	codConto = $("#" + campoCodConto).val();
	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	if (xmlhttp.responseText != "") {
        		$("#" + campoCodConto + "_control_group").addClass("has-error");
        	}
        }
    }
    xmlhttp.open("GET", "controllaContoFacade.class.php?modo=start&codconto=" + codConto, true);
    xmlhttp.send();		
}

//---------------------------------------------------------------------

function modificaGruppoSottoconto(indgruppo,codconto,codsottoconto)
{
	$( "#codconto_modgru" ).val(codconto);
	$( "#codsottoconto_modgru" ).val(codsottoconto);

	$("#indgruppoNS_modgru").parent('.btn').removeClass('active');
	$("#indgruppoCF_modgru").parent('.btn').removeClass('active');
	$("#indgruppoCV_modgru").parent('.btn').removeClass('active');
	$("#indgruppoRC_modgru").parent('.btn').removeClass('active');
	
	if (indgruppo == "NS") {
		$("#indgruppoNS_modgru").parent('.btn').addClass('active');
		$("#indgruppoNS_modgru").prop('checked',true);
	}	
	if (indgruppo == "CF") {
		$("#indgruppoCF_modgru").parent('.btn').addClass('active');
		$("#indgruppoCF_modgru").prop('checked',true);
	}
	if (indgruppo == "CV") {
		$("#indgruppoCV_modgru").parent('.btn').addClass('active');
		$("#indgruppoCV_modgru").prop('checked',true);
	}
	if (indgruppo == "RC") {
		$("#indgruppoRC_modgru").parent('.btn').addClass('active');
		$("#indgruppoRC_modgru").prop('checked',true);
	}

	$("#modifica-gruppo-sottoconto-dialog").modal("show");
}














//function generaMastrino(codconto, codsottoconto) {
//
//	var input_codcontogenera = "<input type='text' id='codcontogenera' name='codcontogenera' value='" + codconto + "' readonly >";
//	var input_codsottocontogenera = "<input type='text' id='codsottocontogenera' name='codsottocontogenera' value='" + codsottoconto + "' readonly >";
//	
//	$( "#id_codcontogenera" ).html(input_codcontogenera);
//	$( "#id_codsottocontogenera" ).html(input_codsottocontogenera);
//	$( "#generaMastrino-form" ).dialog( "open" );
//}
//
//$( "#generaMastrino-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 450,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//				$("#generaMastrino").submit();				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
////---------------------------------------------------------------------------------		
////Creazione di un nuovo sottoconto in modifica conto
////---------------------------------------------------------------------------------		
//$( "#nuovo-sottoconto-modificaconto" ).click(function( event ) {
//	$( "#nuovo-sottoconto-modificaconto-form" ).dialog( "open" );
//	event.preventDefault();
//});
//
//$( "#nuovo-sottoconto-modificaconto-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 650,
//	height: 250,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//           $("#nuovoSottoconto").submit();				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
////---------------------------------------------------------------------------------		
//function cancellaConto(codconto) {
////---------------------------------------------------------------------------------			
//	$( "#codconto" ).val(codconto);
//	$( "#cancella-conto-form" ).dialog( "open" );
//}
//
//$( "#cancella-conto-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 300,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//       $("#cancellaConto").submit();				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
//
//$( "#modifica-sottoconto-modificagruppo-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 500,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				
//				var codconto = $("#codconto_modgru").val();
//				var codsottoconto = $("#codsottoconto_modgru").val();
//				var indgruppo = $("input[name=indgruppo_modgru]:checked").val();				
//				var xmlhttp = new XMLHttpRequest();
//			    xmlhttp.onreadystatechange = function() {
//			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//			        	var sottocontiTable_mod = xmlhttp.responseText;
//		        		$("#sottocontiTable_mod").html(sottocontiTable_mod);
//		        		var sottocontiTable = sottocontiTable_mod.replace("sottocontiTable_mod","sottocontiTable")
//		        		$("#sottocontiTable").html(sottocontiTable);
//			        }
//			    }
//			    xmlhttp.open("GET", "modificaGruppoSottocontoFacade.class.php?modo=start&codconto_modgru=" + codconto + "&codsottoconto_modgru=" + codsottoconto + "&indgruppo_modgru=" + indgruppo, true);
//			    xmlhttp.send();				
//				
//				$(this).dialog('close');
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
////---------------------------------------------------------------------------------			
//// Creazione di un nuovo conto
////---------------------------------------------------------------------------------			
//$( "#nuovo-conto" ).click(function( event ) {
//	$( "#nuovo-conto-form" ).dialog( "open" );
//	event.preventDefault();
//});
//
//$( "#nuovo-conto-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 750,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//				$("#nuovoConto").submit();				
//			}
//		},
//		{
//			text: "Nuovo sottoconto",
//			click: function() {
//				$( "#nuovo-sottoconto-form" ).dialog( "open" );
//				event.preventDefault();
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//				$("#annullaNuovoConto").submit();
//			}
//		}
//	]
//});
//
////---------------------------------------------------------------------------------			
//function modificaConto(codconto) {
////---------------------------------------------------------------------------------				
//	var xmlhttp = new XMLHttpRequest();
//    xmlhttp.onreadystatechange = function() {
//        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//        	var response = xmlhttp.responseText;
//        	
//        	var datiPagina = response.split("|");
//    		$("#sottocontiTable_mod").html(datiPagina[0]);
//    		$("#codconto_mod").val(datiPagina[1]);
//    		$("#desconto_mod").val(datiPagina[2]);    
//    		
//    		if (datiPagina[3] == "Conto Economico") {
//    			$("#contoeco_mod").attr("checked", "checked").button("refresh");
//    		}
//    		else {
//        		if (datiPagina[3] == "Stato Patrimoniale") {
//        			$("#contopat_mod").attr("checked", "checked").button("refresh");
//        		}    			
//    		}
//    		
//    		if (datiPagina[4] == "Dare") {
//    			$("#dare_mod").attr("checked", "checked").button("refresh");
//    		}
//    		else {
//        		if (datiPagina[4] == "Avere") {
//        			$("#avere_mod").attr("checked", "checked").button("refresh");
//        		}    			
//    		}
//    		
//    		if (datiPagina[5] == "S") {
//    			$("#presenzaSi_mod").attr("checked", "checked").button("refresh");
//    		}
//    		else {
//        		if (datiPagina[5] == "N") {
//        			$("#presenzaNo_mod").attr("checked", "checked").button("refresh");
//        		}    			
//    		}
//    		
//    		if (datiPagina[6] == "S") {
//    			$("#sottocontiSi_mod").attr("checked", "checked").button("refresh");
//    		}
//    		else {
//        		if (datiPagina[6] == "N") {
//        			$("#sottocontiNo_mod").attr("checked", "checked").button("refresh");
//        		}    			
//    		}
//
//    		$("#numrigabilancio_mod").val(datiPagina[7]);
//    		$("#slider-posizione-bilancio_mod").slider( "value", datiPagina[7] );	
//    		
//    		$( "#modifica-conto-form" ).dialog( "open" );
//        }
//    }
//    xmlhttp.open("GET", "modificaContoFacade.class.php?modo=start&codconto=" + codconto, true);
//    xmlhttp.send();				
//}
//
//$( "#modifica-conto-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 750,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				$(this).dialog('close');
//				$("#modificaConto").submit();
//			}
//		},
//		{
//			text: "Nuovo sottoconto",
//			click: function() {
//				$( "#nuovo-sottoconto-form" ).dialog( "open" );
//				event.preventDefault();
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//				$("#annullaModificaConto").submit();
//			}
//		}
//	]
//});
//
//
//$( "#nuovo-sottoconto-form" ).dialog({
//	autoOpen: false,
//	modal: true,
//	width: 600,
//	height: 200,
//	buttons: [
//		{
//			text: "Ok",
//			click: function() {
//				
//				var codsottoconto = $("#codsottoconto").val();
//				var dessottoconto = $("#dessottoconto").val();
//				
//				var xmlhttp = new XMLHttpRequest();
//			    xmlhttp.onreadystatechange = function() {
//			        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//			        	var sottocontiTable = xmlhttp.responseText;
//		        		$("#sottocontiTable").html(sottocontiTable);
//		        		$("#sottocontiTable_mod").html(sottocontiTable);
//			        }
//			    }
//			    xmlhttp.open("GET", "aggiungiNuovoSottocontoFacade.class.php?modo=start&codsottoconto=" + codsottoconto + "&dessottoconto=" + dessottoconto, true);
//			    xmlhttp.send();				
//
//				$( this ).dialog( "close" );           				
//			}
//		},
//		{
//			text: "Cancel",
//			click: function() {
//				$( this ).dialog( "close" );
//			}
//		}
//	]
//});
//
//
////---------------------------------------------------------------------------------				
//
//
////---------------------------------------------------------------------------------				
