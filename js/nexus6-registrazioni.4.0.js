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
			$("#nuova-registrazione-dialog").modal("show");
		}
	}
	xmlhttp.open("GET", "creaRegistrazioneFacade.class.php?modo=start", true);
	xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-nuova-scadenza-form").click(function() {
	$("#nuova-data-scadenza-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-dettaglio-nuova-registrazione-form").click(function() {
	$("#nuovo-dettaglio-dialog").modal("show");
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
					$("#dettagli_mod").html(sottocontiTable);
					controllaDettagliRegistrazione("dettagli_cre");
					controllaDettagliRegistrazione("dettagli_mod");
				}
			}
		xmlhttp.open("GET","aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto="	+ conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
		xmlhttp.send();
	}
);		

//---------------------------------------------------------------------------------




// $( "#nuova-registrazione-dialog1" ).dialog({
// autoOpen: false,
// modal: true,
// width: 1000,
// buttons: [
// {
// id: "button-ok-nuova-registrazione-form",
// text: "Ok",
// click: function() {
// $(this).dialog('close');
// $("#nuovaRegistrazione").submit();
// }
// },
// {
// id: "button-dettaglio-nuova-scadenza-form",
// text: "Nuova scadenza",
// click: function() {
// $("#datascad_cre" ).val("");
// $("#newimpscad_cre").val("");
// $("#button-nuova-scadenza-form").button("disable");
// $("#nuova-data-scadenza-form").dialog( "open" );
// }
// },
// {
// id: "button-dettaglio-nuova-registrazione-form",
// text: "Nuovo Dettaglio",
// click: function() {
// $("#button-Ok-nuovo-dettaglio-form").button("disable");
// $("#importo_dett_cre").val("");
// $("#nuovo-dettaglio-registrazione-form").dialog( "open" );
// }
// },
// {
// text: "Cancel",
// click: function() {
// $( this ).dialog( "close" );
//				
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// document.getElementById("nuovaRegistrazione").reset();
// $("#tddettagli_cre").removeClass("inputFieldError");
// $("#messaggioControlloDettagli").html("");
// $("#scadenzesuppl_cre").html("");
// }
// }
// xmlhttp.open("GET", "annullaNuovaRegistrazioneFacade.class.php?modo=start",
// true);
// xmlhttp.send();
// }
// }
// ]
// });

//$("#nuovo-dettaglio-registrazione-form")
//		.dialog(
//				{
//					autoOpen : false,
//					modal : true,
//					width : 550,
//					height : 250,
//					buttons : [
//							{
//								id : "button-Ok-nuovo-dettaglio-form",
//								text : "Ok",
//								click : function() {
//
//									if ($('#dare_dett_cre').is(':checked')) {
//										var D_A = $("#dare_dett_cre").val();
//									}
//									if ($('#avere_dett_cre').is(':checked')) {
//										var D_A = $("#avere_dett_cre").val();
//									}
//
//									var conto = $("#conti").val().replace(",",
//											"."); // tolgo eventuali virgole
//									// nella descrizione del
//									// conto
//									var idconto = conto.substring(0, 6);
//									var importo = $("#importo_dett_cre").val();
//									var importoNormalizzato = importo.trim()
//											.replace(",", ".");
//
//									var xmlhttp = new XMLHttpRequest();
//									xmlhttp.onreadystatechange = function() {
//										if (xmlhttp.readyState == 4
//												&& xmlhttp.status == 200) {
//											var sottocontiTable = xmlhttp.responseText;
//											$("#dettagli_cre").html(
//													sottocontiTable);
//											$("#dettagli_mod").html(
//													sottocontiTable);
//											controllaDettagliRegistrazione("dettagli_cre");
//											controllaDettagliRegistrazione("dettagli_mod");
//										}
//									}
//									xmlhttp
//											.open(
//													"GET",
//													"aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto="	+ conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato,
//													true);
//									xmlhttp.send();
//
//									$(this).dialog("close");
//								}
//							}, {
//								text : "Cancel",
//								click : function() {
//									$(this).dialog("close");
//								}
//							} ]
//				});

// ---------------------------------------------------------------------------------
// Modifica di una registrazione
// ---------------------------------------------------------------------------------
$("#modifica-registrazione-form")
		.dialog(
				{
					autoOpen : false,
					modal : true,
					width : 1000,
					buttons : [
							{
								id : "button-ok-modifica-registrazione-form",
								text : "Ok",
								click : function() {
									$(this).dialog('close');
									$("#modificaRegistrazione").submit();
								}
							},
							{
								id : "button-dettaglio-nuova-scadenza-form",
								text : "Nuova scadenza",
								click : function() {
									$("#datascad_mod").val("");
									$("#newimpscad_mod").val("");
									$("#button-nuova-scadenza-modifica-form")
											.button("disable");
									$("#nuova-data-scadenza-modifica-form")
											.dialog("open");
								}
							},
							{
								id : "button-dettaglio-modifica-registrazione-form",
								text : "Nuovo Dettaglio",
								click : function() {
									$("#button-Ok-nuovo-dettaglio-form")
											.button("disable");
									$("#importo_dett_cre").val("");
									$("#nuovo-dettaglio-registrazione-form")
											.dialog("open");
								}
							},
							{
								text : "Cancel",
								click : function() {
									$(this).dialog("close");

									var xmlhttp = new XMLHttpRequest();
									xmlhttp.onreadystatechange = function() {
										if (xmlhttp.readyState == 4
												&& xmlhttp.status == 200) {
											document.getElementById(
													"modificaRegistrazione")
													.reset();
											$("#tddettagli_mod").removeClass(
													"inputFieldError");
											$("#messaggioControlloDettagli_mod")
													.html("");
										}
									}
									xmlhttp
											.open(
													"GET",
													"annullaModificaRegistrazioneFacade.class.php?modo=start",
													true);
									xmlhttp.send();
								}
							} ]
				});

// ---------------------------------------------------------------------------------
// CREA REGISTRAZIONE : controllo campi in pagina
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

// ---------------------------------------------------------------------------------

function controllaDataRegistrazione(campoDat) {
	/**
	 * La data registrazione è obbligatoria Il controllo sulla data
	 * registrazione verificha che la data immessa cada all'interno di uno dei
	 * mesi in linea. I mesi in linea coincidono con le date pianificate di
	 * riporto saldo
	 * 
	 */
	var datareg = $("#" + campoDat).val();

	if (datareg != "") {

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#" + campoDat + "_messaggio").html(xmlhttp.responseText);
					$("#" + campoDat + "_control_group").addClass("has-error");
				} else {
					$("#" + campoDat + "_messaggio").html("");
					$("#" + campoDat + "_control_group").removeClass(
							"has-error");
				}
			}
		}
		xmlhttp.open("GET",
				"controllaDataRegistrazioneFacade.class.php?modo=start&datareg="
						+ datareg, true);
		xmlhttp.send();
	} else {
		$("#" + campoMsg).html("Dato errato");
		$("#" + campoDatErr).addClass("inputFieldError");
	}
}

// ---------------------------------------------------------------------------------

function controllaDescrizione(campoDes) {
	/**
	 * La descrizione della registrazione è obbligatoria
	 */
	if ($("#" + campoDes).val() != "") {
		$("#" + campoDes + "_control_group").removeClass("has-error");
		$("#" + campoDes + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoDes + "_control_group").addClass("has-error");
		$("#" + campoDes + "_messaggio").html("obbligatorio");
		return false;
	}
}

//---------------------------------------------------------------------------------

function controllaCausale(campoCau) {
	/**
	 * La causale è obbligatoria
	 */
	if ($("#" + campoCau).val() != "") {
		$("#" + campoCau + "_control_group").removeClass("has-error");
		$("#" + campoCau + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoCau + "_control_group").addClass("has-error");
		$("#" + campoCau + "_messaggio").html("obbligatoria");
		return false;
	}
}

// ---------------------------------------------------------------------

function controllaClienteFornitore(campoForn, campoCli) {
	/**
	 * Il cliente e il fornitore sono mutualmente esclusivi Possono mancare
	 * entrambi
	 */
	if (($("#" + campoForn).val() != "") && ($("#" + campoCli).val() != "")) {

		$("#" + campoForn + "_control_group").addClass("has-error");
		$("#" + campoCli + "_control_group").addClass("has-error");
		$("#" + campoForn + "_messaggio").html("Dato errato");
		$("#" + campoCli + "_messaggio").html("Dato errato");
		return false;
	} else if (($("#" + campoForn).val() == "") && ($("#" + campoCli).val() == "")) {
		$("#" + campoForn + "_control_group").addClass("has-error");
		$("#" + campoCli + "_control_group").addClass("has-error");
		$("#" + campoForn + "_messaggio").html("Esclusivo");
		$("#" + campoCli + "_messaggio").html("Esclusivo");
		return false;
	} else {
		$("#" + campoForn + "_control_group").removeClass("has-error");
		$("#" + campoCli + "_control_group").removeClass("has-error");
		$("#" + campoForn + "_messaggio").html("");
		$("#" + campoCli + "_messaggio").html("");
		return true;
	}
}

// ---------------------------------------------------------------------

function controllaNumeroFattura(campoFat) {
	var numfatt = $("#" + campoFat).val();

	if (numfatt != "") {
		$("#" + campoFat + "_control_group").removeClass("has-error");
		$("#" + campoFat + "_messaggio").html("");
		return true;
	} else {
		$("#" + campoFat + "_control_group").addClass("has-error");
		$("#" + campoFat + "_messaggio").html("obbligatoria");
		return false;
	}
}

// ---------------------------------------------------------------------

function controllaNumeroFatturaFornitore(campoForn, campoFat, campoDat) {
	/**
	 * La fattura del fornitore immessa deve essere univoca
	 */
	var fornitore = $("#" + campoForn).val();
	var numfatt = $("#" + campoFat).val();
	var numfattOrig = $("#" + campoFat + "_orig").val();
	var datareg = $("#" + campoDat).val();

	if ((numfatt != "") && (datareg != "") && (fornitore != "")) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					if (numfatt != numfattOrig) {
						$("#" + campoFat + "_control_group").addClass("has-error");
						$("#" + campoForn + "_control_group").addClass("has-error");
						$("#" + campoFat + "_messaggio").html(xmlhttp.responseText);
					}					
					else {
						$("#" + campoFat + "_control_group").removeClass("has-error");
						$("#" + campoForn + "_control_group").removeClass("has-error");
						$("#" + campoFat + "_control_group").html("");
					}
				}
				else {
					$("#" + campoFat + "_control_group").removeClass("has-error");
					$("#" + campoForn + "_control_group").removeClass("has-error");
					$("#" + campoFat + "_control_group").html("");
				}
			}
		}
		xmlhttp.open("GET","cercaFatturaFornitoreFacade.class.php?modo=start&desfornitore=" + fornitore + "&numfatt=" + numfatt + "&datareg=" + datareg, true);
		xmlhttp.send();
	} 
	else return true;
}

// ---------------------------------------------------------------------

function controllaNumeroFatturaCliente(campoCli, campoFat, campoDat) {
	/**
	 * La fattura del cliente immessa deve essere univoca
	 */
	var cliente = $("#" + campoCli).val();
	var numfatt = $("#" + campoFat).val();
	var datareg = $("#" + campoDat).val();

	if ((numfatt != "") && (datareg != "") && (cliente != "")) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText != "") {
					$("#" + campoFat + "_control_group").addClass("has-error");
					$("#" + campoCli + "_control_group").addClass("has-error");
					$("#" + campoFat + "_control_group").html(xmlhttp.responseText);
				}
				else {
					$("#" + campoFat + "_control_group").removeClass("has-error");
					$("#" + campoCli + "_control_group").removeClass("has-error");
					$("#" + campoFat + "_control_group").html("");
				}
			}
		}
		xmlhttp.open("GET","cercaFatturaClienteFacade.class.php?modo=start&descliente=" + cliente + "&numfatt=" + numfatt + "&datareg=" + datareg, true);
		xmlhttp.send();
	}
	else return true;
}

// ---------------------------------------------------------------------

function controllaDettagliRegistrazione(campoDet)
{
	/**
	 * I dettagli della registrazione devono essere presenti Gli importi del
	 * Dare e Avere devono quadrare
	 */
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			if (xmlhttp.responseText != "") {
				$("#" + campoDet + "_control_group").addClass("has-error");
				$("#" + campoDet + "_messaggio").html("Completa i dettagli");
			} else {
				$("#" + campoDet + "_control_group").removeClass("has-error");
				$("#" + campoDet + "_messaggio").html("");
			}
		}
	}
	xmlhttp.open("GET","verificaDettagliRegistrazioneFacade.class.php?modo=start", true);
	xmlhttp.send();
}

//---------------------------------------------------------------------

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
		 xmlhttp.open("GET","aggiornaImportoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&importo=" + importoDett + "&iddettaglio=" + idDettaglio, true);
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
		xmlhttp.open("GET", "aggiornaSegnoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&dareAvere=" + segno + "&iddettaglio=" + idDettaglio, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------

function aggiungiDettaglioContoFornitore(fornitore, campoDett)
{
	if (fornitore != "") {
		$("#button-dettaglio-nuova-registrazione-form").prop("disabled", false);
		var fornitoreNorm = fornitore.replace("&", ""); // tolgo eventuali &
		// nella ragione sociale

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
		xmlhttp.open("GET", "aggiungiNuovoDettaglioContoFornitoreFacade.class.php?modo=go&desfornitore=" + fornitoreNorm, true);
		xmlhttp.send();
	}
}

//---------------------------------------------------------------------

function aggiungiDettaglioContoCliente(cliente, campoDett, campoMsg, campoDes, campoDesLabel)
{
	if (cliente != "") {
		var clienteNorm = cliente.replace("&", ""); // tolgo eventuali & nella
		// ragione sociale

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
		xmlhttp.open("GET","aggiungiNuovoDettaglioContoClienteFacade.class.php?modo=go&descliente=" + clienteNorm, true);
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
	xmlhttp.open("GET", "cancellaScadenzaFornitoreFacade.class.php?modo=start&idfornitore=" + idFornitore + "&datascad_for=" + datScad + "&numfatt=" + numFatt, true);
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
	xmlhttp.open("GET", "cancellaScadenzaClienteFacade.class.php?modo=start&idcliente=" + idCliente + "&datascad_cli=" + datScad + "&numfatt=" + numFatt, true);
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
		xmlhttp.open("GET","aggiornaImportoScadenzaFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore + "&datascad_for=" + datascad + "&numfatt=" + numfatt + "&impscad_for=" + importoScad, true);
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
		xmlhttp.open("GET", "aggiornaImportoScadenzaClienteFacade.class.php?modo=go&idcliente=" + idcliente + "&datascad_cli=" + datascad + "&numfatt=" + numfatt + "&impscad_cli=" + importoScad, true);
		xmlhttp.send();
	}
}







// ---------------------------------------------------------------------------------
// MODIFICA REGISTRAZIONE : controllo campi in pagina
// ---------------------------------------------------------------------------------

function validaModificaRegistrazione() {
	/**
	 * Ciascun controllo di validazione può dare un esito positivo (1) o
	 * negativo (0) La validazione complessiva è positiva se tutti i controlli
	 * sono positivi (1) Se la validazione è positiva viene abilitato il bottone
	 * ok di conferma inserimento
	 */
	var esito = "";

	controllaDataRegistrazione("datareg_mod", "tddatareg_mod",
			"messaggioControlloDataRegistrazione_mod");
	if ($("#messaggioControlloDataRegistrazione_mod").text() == "")
		esito = esito + "1";
	else
		esito = esito + "0";

	if ($("#descreg_mod").val() != "") {
		if (controllaDescrizione("descreg_mod", "tddescreg_mod",
				"messaggioControlloDescrizione_mod"))
			esito = esito + "1";
		else
			esito = esito + "0";
	}

	if (controllaClienteFornitore("fornitore_mod", "cliente_mod",
			"tdfornitore_mod", "tdcliente_mod", "tdnumfatt_mod",
			"messaggioControlloFornitore_mod", "messaggioControlloCliente_mod",
			"messaggioControlloNumeroFattura_mod"))
		esito = esito + "1";
	else
		esito = esito + "0";

	if (($("#fornitore_mod").val() != "") || $("#cliente_mod").val() != "") {
		if ($("#numfatt_mod").val() != $("#numfatt_mod_orig").val()) {
			if (controllaNumeroFattura("numfatt_mod", "tdnumfatt_mod",
					"messaggioControlloNumeroFattura_mod"))
				esito = esito + "1";
			else
				esito = esito + "0";
		} else {
			esito = esito + "1";
		}
	}

	if ($("#fornitore_mod").val() != "") {
		if ($("#numfatt_mod").val() != $("#numfatt_mod_orig").val()) {
			controllaNumeroFatturaFornitore("fornitore_mod", "numfatt_mod",
					"datareg_mod", "tdnumfatt_mod",
					"messaggioControlloNumeroFattura_mod");
			if ($("#messaggioControlloNumeroFattura_mod").text() == "")
				esito = esito + "1";
			else
				esito = esito + "0";
		} else {
			esito = esito + "1";
		}

	}

	if ($("#cliente_mod").val() != "") {
		if ($("#numfatt_mod").val() != $("#numfatt_mod_orig").val()) {
			controllaNumeroFatturaCliente("cliente_mod", "numfatt_mod",
					"datareg_mod", "tdnumfatt_mod",
					"messaggioControlloNumeroFattura_mod");
			if ($("#messaggioControlloNumeroFattura_mod").text() == "")
				esito = esito + "1";
			else
				esito = esito + "0";
		} else {
			esito = esito + "1";
		}

	}

	if ($("#causale_mod").val() != "") {
		controllaDettagliRegistrazione("dettagli_mod");
		if ($("#messaggioControlloDettagli_mod").text() == "")
			esito = esito + "1";
		else
			esito = esito + "0";
	}

	if (esito == "111111") {
		$("#button-ok-modifica-registrazione-form").button("enable");
	} else {
		$("#button-ok-modifica-registrazione-form").button("disable");
	}
}

// ---------------------------------------------------------------------

function modificaRegistrazione(idRegistrazione) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			document.getElementById("modificaRegistrazione").reset();

			var response = xmlhttp.responseText;
			var datiPagina = response.split("|");
			var datareg_mod = datiPagina[0];
			var descreg_mod = datiPagina[1];
			var causale_mod = datiPagina[2];
			var codneg_mod = datiPagina[3];
			var fornitore_mod = datiPagina[4];
			var cliente_mod = datiPagina[5];
			var numfatt_mod = datiPagina[6];
			var numfattorig_mod = datiPagina[7];
			var scadenzesuppl_fornitore_mod = datiPagina[8];
			var scadenzesuppl_cliente_mod = datiPagina[9];
			var dettagli_mod = datiPagina[10];
			var conti = datiPagina[11];

			$("#datareg_mod").val(datareg_mod);
			$("#descreg_mod").val(descreg_mod);
			$("#causale_mod").val(causale_mod);
			$("#causale_mod").selectmenu("refresh");

			if (codneg_mod == "VIL") {
				$("#villa_mod").prop("checked", true).button("refresh");
			} else {
				if (codneg_mod == "BRE") {
					$("#brembate_mod").prop("checked", true).button("refresh");
				} else {
					if (codneg_mod == "TRE") {
						$("#trezzo_mod").prop("checked", true)
								.button("refresh");
					}
				}
			}

			$("#fornitore_mod").val(fornitore_mod);
			$("#cliente_mod").val(cliente_mod);
			$("#numfatt_mod").val(numfatt_mod);
			$("#numfatt_mod_orig").val(numfattorig_mod);

			if (fornitore_mod != "")
				$("#scadenzesuppl_mod").html(scadenzesuppl_fornitore_mod);
			if (cliente_mod != "")
				$("#scadenzesuppl_mod").html(scadenzesuppl_cliente_mod);

			$("#dettagli_mod").html(dettagli_mod);
			$("#conti").html(conti);
			$("#conti").selectmenu("refresh");

			$("#modifica-registrazione-form").dialog("open");
		}
	}
	xmlhttp.open("GET",
			"modificaRegistrazioneFacade.class.php?modo=start&idreg="
					+ idRegistrazione, true);
	xmlhttp.send();
}

// ---------------------------------------------------------------------------------
// funzioni x le scadenze
// ---------------------------------------------------------------------------------

$("#nuova-data-scadenza-form").dialog(
		{
			autoOpen : false,
			modal : true,
			width : 550,
			height : 180,
			buttons : [
					{
						id : "button-nuova-scadenza-form",
						text : "Ok",
						click : function() {

							var datascad = $("#newdatascad_cre").val();
							var impscad = $("#newimpscad_cre").val();
							var fornitore = $("#fornitore_cre").val();
							var cliente = $("#cliente_cre").val();
							var numfatt = $("#numfatt_cre").val();

							if (fornitore != "") {
								var xmlhttp = new XMLHttpRequest();
								xmlhttp.onreadystatechange = function() {
									if (xmlhttp.readyState == 4
											&& xmlhttp.status == 200) {
										if (xmlhttp.responseText != "") {
											$("#datascad_cre_label").show();
											$("#scadenzesuppl_cre").html(
													xmlhttp.responseText);
										} else {
											$("#datascad_cre_label").hide();
											$("#scadenzesuppl_cre").html("");
											$("#scadenzesuppl_cre").hide();
										}
									}
								}
								xmlhttp.open("GET",
										"aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore="
												+ fornitore + "&datascad_for="
												+ datascad + "&impscad_for="
												+ impscad + "&numfatt="
												+ numfatt, true);
								xmlhttp.send();
							} else if (cliente != "") {
								var xmlhttp = new XMLHttpRequest();
								xmlhttp.onreadystatechange = function() {
									if (xmlhttp.readyState == 4
											&& xmlhttp.status == 200) {
										if (xmlhttp.responseText != "") {
											$("#datascad_cre_label").show();
											$("#scadenzesuppl_cre").html(
													xmlhttp.responseText);
										} else {
											$("#datascad_cre_label").hide();
											$("#scadenzesuppl_cre").html("");
											$("#scadenzesuppl_cre").hide();
										}
									}
								}
								xmlhttp.open("GET",
										"aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&datascad_cli="
												+ datascad + "&cliente="
												+ cliente + "&impscad_cli="
												+ impscad + "&numfatt="
												+ numfatt, true);
								xmlhttp.send();
							}
							$(this).dialog("close");
						}
					}, {
						text : "Cancel",
						click : function() {
							$(this).dialog("close");
						}
					} ]
		});

// ---------------------------------------------------------------------------------

$("#nuova-data-scadenza-modifica-form").dialog(
		{
			autoOpen : false,
			modal : true,
			width : 550,
			height : 180,
			buttons : [
					{
						id : "button-nuova-scadenza-modifica-form",
						text : "Ok",
						click : function() {

							var datascad = $("#newdatascad_mod").val();
							var impscad = $("#newimpscad_mod").val();
							var fornitore = $("#fornitore_mod").val();
							var cliente = $("#cliente_mod").val();
							var numfatt = $("#numfatt_mod").val();

							if (fornitore != "") {
								var xmlhttp = new XMLHttpRequest();
								xmlhttp.onreadystatechange = function() {
									if (xmlhttp.readyState == 4
											&& xmlhttp.status == 200) {
										if (xmlhttp.responseText != "") {
											$("#datascad_mod_label").show();
											$("#scadenzesuppl_mod").html(
													xmlhttp.responseText);
										} else {
											$("#datascad_mod_label").hide();
											$("#scadenzesuppl_mod").html("");
											$("#scadenzesuppl_mod").hide();
										}
									}
								}
								xmlhttp.open("GET",
										"aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore="
												+ fornitore + "&datascad_for="
												+ datascad + "&impscad_for="
												+ impscad + "&numfatt="
												+ numfatt, true);
								xmlhttp.send();
							} else if (cliente != "") {
								var xmlhttp = new XMLHttpRequest();
								xmlhttp.onreadystatechange = function() {
									if (xmlhttp.readyState == 4
											&& xmlhttp.status == 200) {
										if (xmlhttp.responseText != "") {
											$("#datascad_mod_label").show();
											$("#scadenzesuppl_mod").html(
													xmlhttp.responseText);
										} else {
											$("#datascad_mod_label").hide();
											$("#scadenzesuppl_mod").html("");
											$("#scadenzesuppl_mod").hide();
										}
									}
								}
								xmlhttp.open("GET",
										"aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&datascad_cli="
												+ datascad + "&cliente="
												+ cliente + "&impscad_cli="
												+ impscad + "&numfatt="
												+ numfatt, true);
								xmlhttp.send();
							}
							$(this).dialog("close");
						}
					}, {
						text : "Cancel",
						click : function() {
							$(this).dialog("close");
						}
					} ]
		});



// ---------------------------------------------------------------------------------

function controllaImporto(campoImp) {

	var importo = $("#" + campoImp).val();

	if (isNumeric(importo)) {
		$("#" + campoImp + "_control_group").removeClass("has-error");
		$("#" + campoImp + "_messaggio").html("");
	} else {
		$("#" + campoImp + "_control_group").addClass("has-error");
		$("#" + campoImp + "_messaggio").html("non valido");
	}
}

// ---------------------------------------------------------------------------------

//function modificaDataScadenzaFornitore(idTable, idfornitore, datascad, numfatt,
//		datascadnew) {
//	if (datascad != "") {
//		var xmlhttp = new XMLHttpRequest();
//		xmlhttp.onreadystatechange = function() {
//			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//				if (xmlhttp.responseText != "") {
//					var scadenzeTable = xmlhttp.responseText;
//					$("#" + idTable).html(scadenzeTable);
//				}
//			}
//		}
//		xmlhttp.open("GET",
//				"aggiornaDataScadenzaFornitoreFacade.class.php?modo=go&idfornitore="
//						+ idfornitore + "&datascad_for=" + datascad
//						+ "&numfatt=" + numfatt + "&datascad_new="
//						+ datascadnew, true);
//		xmlhttp.send();
//	}
//}

// ---------------------------------------------------------------------------------
// Funzioni per clienti e fornitori
// ---------------------------------------------------------------------------------

$("#fornitore_cre").change(
	function() {

		var desfornitore = $("#fornitore_cre").val();
		var datareg = $("#datareg_cre").val();

		if (desfornitore != "") {
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
				xmlhttp.open("GET","calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore="+ desfornitore + "&datareg=" + datareg, true);
				xmlhttp.send();
		}
	}
);

// ---------------------------------------------------------------------------------

$("#cliente_cre").change(
	function() {

		var descliente = $("#cliente_cre").val();
		var datareg = $("#datareg_cre").val();

		if (descliente != "") {
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
			xmlhttp.open("GET",
					"calcolaDataScadenzaClienteFacade.class.php?modo=start&descliente="
							+ descliente + "&datareg=" + datareg, true);
			xmlhttp.send();
		}
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
						$("#conti").selectmenu("refresh");
					}
				}
			xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
			xmlhttp.send();

		}
	}
);

// ---------------------------------------------------------------------------------

$(".selectmenuCausaleMod").selectmenu(
		{
			change : function() {
				var causale = $("#causale_mod").val();

				if (causale != "") {
					$("#tdcausale_mod").removeClass("inputFieldError");
					$("#messaggioControlloCausale_mod").html("");

					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							$("#conti").html(xmlhttp.responseText);
							$("#conti").selectmenu("refresh");
							$("#button-dettaglio-modifica-registrazione-form")
									.button("enable");
							validaModificaRegistrazione();
						}
					}
					xmlhttp.open("GET",
							"loadContiCausaleFacade.class.php?modo=start&causale="
									+ causale, true);
					xmlhttp.send();

				} else {
					$("#tdcausale_mod").addClass("inputFieldError");
					$("#messaggioControlloCausale_mod").html("Dato errato");
					$("#button-dettaglio-modifica-registrazione-form").button(
							"disable");
				}
			}
		}).selectmenu({
	width : 300
}).selectmenu("menuWidget").addClass("overflow");

// ---------------------------------------------------------------------------------

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

// ---------------------------------------------------------------------------------

function cancellaRegistrazione(idreg) {
	$("#idRegistrazione").val(idreg);
	$("#cancella-registrazione-form").dialog("open");
}

$("#cancella-registrazione-form").dialog({
	autoOpen : false,
	modal : true,
	width : 300,
	buttons : [ {
		text : "Ok",
		click : function() {
			$(this).dialog('close');
			$("#cancellaRegistrazione").submit();
		}
	}, {
		text : "Cancel",
		click : function() {
			$(this).dialog("close");
		}
	} ]
});

// ---------------------------------------------------------------------------------
// vecchie funzioni non ancor utilizzate dalla nuova versione 4.0
// ---------------------------------------------------------------------------------

// $( "#nuovo-dett-modificareg" ).click(function( event ) {
// $( "#nuovo-dettaglio-modificareg-form" ).dialog( "open" );
// event.preventDefault();
// });
//
// $( "#nuovo-dettaglio-modificareg-form" ).dialog({
// autoOpen: false,
// modal: true,
// width: 550,
// height: 400,
// buttons: [
// {
// text: "Ok",
// click: function() {
//				
// // Controllo congruenza conto dettaglio
//				
// var conto = $("#conti").val();
// var fornitore = $("#fornitore").val();
// var cliente = $("#cliente").val();
//
// // Fornitore
//				
// if (fornitore != "") {
//
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// if (xmlhttp.responseText == "Dettaglio ok") {
//								
// $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
// $("#nuovoDettaglio").submit();
//				        		
// }
// else {
// $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
// }
// }
//				        
// }
// xmlhttp.open("GET",
// "controlloContoDettaglioPagamentoFacade.class.php?modo=start&fornitore=" +
// fornitore + "&conto=" + conto, true);
// xmlhttp.send();
// }
//
// // Cliente
//				
// else if (cliente != "") {
//
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// if (xmlhttp.responseText == "Dettaglio ok") {
//								
// $( "#esitoControlloContoDettaglio" ).html("&nbsp;");
// $("#nuovoDettaglio").submit();
//				        		
// }
// else {
// $( "#esitoControlloContoDettaglio" ).html(xmlhttp.responseText);
// }
// }
//				        
// }
// xmlhttp.open("GET",
// "controlloContoDettaglioIncassoFacade.class.php?modo=start&cliente=" +
// cliente + "&conto=" + conto, true);
// xmlhttp.send();
// }
// else {
// $("#nuovoDettaglio").submit();
// }
//				
// $( this ).dialog( "close" );
// }
// },
// {
// text: "Cancel",
// click: function() {
// $( this ).dialog( "close" );
// }
// }
// ]
// });
//
// //---------------------------------------------------------------------------------
//
// $( "#nuova-scad-modificareg" ).click(function( event ) {
// $( "#nuova-scadenza-modificareg-form" ).dialog( "open" );
// event.preventDefault();
// });
//
// $( "#nuova-scadenza-modificareg-form" ).dialog({
// autoOpen: false,
// modal: true,
// width: 550,
// height: 150,
// buttons: [
// {
// text: "Ok",
// click: function() {
// $(this).dialog('close');
// $("#nuovaScadenza").submit();
// }
// },
// {
// text: "Cancel",
// click: function() {
// $( this ).dialog( "close" );
// }
// }
// ]
// });
//
// //---------------------------------------------------------------------------------
//
// function cancellaDettaglio(idconto) {
// $( "#idDettaglioRegistrazione" ).val(idconto);
// $( "#cancella-dettaglio-modificareg-form" ).dialog( "open" );
// }
// $( "#cancella-dettaglio-modificareg-form" ).dialog({
// autoOpen: false,
// modal: true,
// width: 300,
// buttons: [
// {
// text: "Ok",
// click: function() {
// $(this).dialog('close');
// $("#cancellaDettaglio").submit();
// }
// },
// {
// text: "Cancel",
// click: function() {
// $( this ).dialog( "close" );
// }
// }
// ]
// });

// ---------------------------------------------------------------------------------

// ---------------------------------------------------------------------------------
// function cancellaScadenza(idscadenza) {
// //---------------------------------------------------------------------------------
// $( "#idScadenzaRegistrazione" ).val(idscadenza);
// $( "#cancella-scadenza-modificareg-form" ).dialog( "open" );
// }
//
// $( "#cancella-scadenza-modificareg-form" ).dialog({
// autoOpen: false,
// modal: true,
// width: 300,
// buttons: [
// {
// text: "Ok",
// click: function() {
// $(this).dialog('close');
// $("#cancellaScadenza").submit();
// }
// },
// {
// text: "Cancel",
// click: function() {
// $( this ).dialog( "close" );
// }
// }
// ]
// });

// ---------------------------------------------------------------------------------

// $( "#fornitore" ).change(function() {
//	
// var desfornitore = $("#fornitore").val();
// var datareg = $("#datareg").val();
// var form = $("#pagamentoForm").val();
//	
// if (desfornitore != "") {
//
// if (form == "PAGAMENTO") {
//			
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// $( "#select2" ).html(xmlhttp.responseText);
// $( "#select2" ).selectmenu( "refresh" );
// }
// }
// xmlhttp.open("GET",
// "ricercaScadenzeAperteFornitoreFacade.class.php?modo=start&desforn=" +
// desfornitore, true);
// xmlhttp.send();
// }
// else {
//			
// /**
// * Data scadenza
// */
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// $( "#datascad" ).val(xmlhttp.responseText);
// $( "#tddatascad").removeClass("inputFieldError");
// $( "#esitoDatascad" ).val("");
// $( "#messaggioControlloDataScadenza" ).html("");
// }
// }
// xmlhttp.open("GET",
// "calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore=" +
// desfornitore + "&datareg=" + datareg, true);
// xmlhttp.send();
// }
// }
// });

// ---------------------------------------------------------------------------------

// $( "#fornitore_regrap" ).change(function() {
//	
// var desfornitore = $("#fornitore_regrap").val();
//	
// if (desfornitore != "") {
// /**
// * Genero i dettagli della registrazione
// */
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// $( "#dettagli" ).html(xmlhttp.responseText);
// }
// }
// xmlhttp.open("GET",
// "aggiungiFornitoreDettagliRegistrazioneFacade.class.php?modo=start&desfornitore="
// + desfornitore, true);
// xmlhttp.send();
// }
// });
//
// //---------------------------------------------------------------------------------
//
// $( "#cliente_regrap" ).change(function() {
//	
// var descliente = $("#cliente_regrap").val();
//	
// if (descliente != "") {
// /**
// * Genero i dettagli della registrazione
// */
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// $( "#dettagli" ).html(xmlhttp.responseText);
// }
// }
// xmlhttp.open("GET",
// "aggiungiClienteDettagliRegistrazioneFacade.class.php?modo=start&descliente="
// + descliente, true);
// xmlhttp.send();
// }
// });

// ---------------------------------------------------------------------------------

// $( "#cliente" ).change(function() {
//	
// var descliente = $("#cliente").val();
//
// if (descliente != "") {
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// $( "#tipoadd" ).val(xmlhttp.responseText);
// }
// }
// xmlhttp.open("GET",
// "prelevaTipoAddebitoClienteFacade.class.php?modo=start&descliente=" +
// descliente, true);
// xmlhttp.send();
// }
// })

// ---------------------------------------------------------------------------------

// $( ".selectmenuCausale" )
// .selectmenu({width: 350})
// .selectmenu("menuWidget")
// .addClass("overflow");
//
// $( ".selectmenuCausaleCre" )
// .selectmenu({width: 350})
// .selectmenu("menuWidget")
// .addClass("overflow");

// ---------------------------------------------------------------------------------

// $('#numeroFatturaFornitore').change(function() {
// var fornitore = $("#fornitore").val();
// var numfatt = $("#numfatt").val();
// var causale = $("#causale").val();
//	
// var xmlhttp = new XMLHttpRequest();
// xmlhttp.onreadystatechange = function() {
// if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
// $( "#esitoControlloNumeroFattura" ).html(xmlhttp.responseText);
// }
// }
// xmlhttp.open("GET",
// "cercaFatturaFornitoreFacade.class.php?modo=start&idfornitore=" + fornitore +
// "&numfatt=" + numfatt, true);
// xmlhttp.send();
// });
