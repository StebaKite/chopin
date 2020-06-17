//---------------------------------------------------------------------------------
// Registrazioni
//---------------------------------------------------------------------------------

//---------------------------------------------------------------------------------
// Creazione di una nuova registrazione
//---------------------------------------------------------------------------------
$("#nuovaRegistrazione").click(function () {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            
            // pulizia degli elementi in pagina
            document.getElementById("nuovaRegistrazioneForm").reset();
            $("#descreg_cre_control_group").removeClass("has-error");
            $("#codneg_cre_control_group").removeClass("has-error");
            $("#causale_cre_control_group").removeClass("has-error");
            $("#datareg_cre_control_group").removeClass("has-error");
            $("#numfatt_cre_control_group").removeClass("has-error");
            $("#fornitore_cre_control_group").removeClass("has-error");            
            $("#cliente_cre_control_group").removeClass("has-error");            
            $("#scadenzesuppl_cre_control_group").removeClass("has-error");
            $("#dettagli_cre_control_group").removeClass("has-error");
            $("#codneg_cre").selectpicker('val', ' ');
            $("#causale_cre").selectpicker('val', ' ');
            $("#fornitore_cre").selectpicker('val', ' ');
            $("#cliente_cre").selectpicker('val', ' ');
            $("#codneg_cre_messaggio").html("");
            $("#descreg_cre_messaggio").html("");
            $("#causale_cre_messaggio").html("");
            $("#numfatt_cre_messaggio").html("");
            $("#scadenzesuppl_cre_messaggio").html("");
            $("#scadenzesuppl_cre").html("");
            $("#dettagli_cre").html("");
            
            // pulizia delle altre tabelle incluse nella pagina ricerca registrazioni
            $("#dettagli_mod").html("");
            $("#dettagli_inc_cre").html("");
            $("#dettagli_inc_mod").html("");
            $("#dettagli_pag_cre").html("");
            $("#dettagli_pag_mod").html("");
            $("#dettagli_cormer_cre").html("");
            $("#dettagli_cormer_mod").html("");
            $("#dettagli_corneg_cre").html("");
            $("#dettagli_corneg_mod").html("");
            
            // pulizia del messaggio
            $("#dettagli_cre_messaggio").html("");
            $("#nuova-registrazione-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "creaRegistrazioneFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-nuova-scadenza-nuova-registrazione-form").click(function () {

    if (isNotEmpty($("#fornitore_cre").val())) {
        $("#nuovaDataScadenzaCreazioneForm").attr("action", "../primanota/aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=go");
    } else if (isNotEmpty($("#cliente_cre").val())) {
        $("#nuovaDataScadenzaCreazioneForm").attr("action", "../primanota/aggiungiNuovaScadenzaClienteFacade.class.php?modo=go");
    }
    $("#nuova-data-scadenza-creazione-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuova-scadenza-modifica-registrazione-form").click(function () {

    if (isNotEmpty($("#fornitore_mod").val())) {
        $("#nuovaDataScadenzaModificaForm").attr("action", "../primanota/aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=go");
    } else if (isNotEmpty($("#cliente_mod").val())) {
        $("#nuovaDataScadenzaCreazioneForm").attr("action", "../primanota/aggiungiNuovaScadenzaClienteFacade.class.php?modo=go");
    }
    $("#nuova-data-scadenza-modifica-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuova-registrazione-form").click(function () {
    $("#nuovo-dettaglio-creazione-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-registrazione-form").click(function () {
    $("#nuovo-dettaglio-modifica-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuova-registrazione-form").click(
        function () {
            if (validaNuovaRegistrazione()) {
                $("#testo-messaggio-successo").html("Registrazione salvata con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("nuova-registrazione-dialog").modal("hide");
                $("#nuovaRegistrazioneForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore la registrazione non può essere salvata");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-registrazione-form").click(
        function () {
            if (validaModificaRegistrazione()) {
                $("#testo-messaggio-successo").html("Registrazione salvata con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("modifica-registrazione-dialog").modal("hide");
                $("#modificaRegistrazioneForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore la registrazione non può essere salvata");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovascad-nuova-registrazione-form").click(
        function () {
            var datascad = $("#newdatascad_cre").val();
            var impscad = $("#newimpscad_cre").val();
            var fornitore = $("#fornitore_cre").val();
            var cliente = $("#cliente_cre").val();
            var numfatt = $("#numfatt_cre").val();

            if (isNotEmpty(fornitore))
            {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        if (isNotEmpty(xmlhttp.responseText)) {
                            $("#scadenzesuppl_cre").html(xmlhttp.responseText);
                            controllaDettagliRegistrazione("scadenzesuppl_cre");
                        }
                    }
                };
                xmlhttp.open("GET", "aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore=" + fornitore + "&datascad_for=" + datascad + "&impscad_for=" + impscad + "&numfatt=" + numfatt, true);
                xmlhttp.send();
            } else if (isNotEmpty(cliente))
            {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        if (isNotEmpty(xmlhttp.responseText)) {
                            $("#scadenzesuppl_cre").html(xmlhttp.responseText);
                            controllaDettagliRegistrazione("scadenzesuppl_cre");                            
                        }
                    }
                };
                xmlhttp.open("GET", "aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&datascad_cli=" + datascad + "&cliente=" + cliente + "&impscad_cli=" + impscad + "&numfatt=" + numfatt, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovascad-modifica-registrazione-form").click(
        function () {
            var datascad = $("#newdatascad_mod").val();
            var impscad = $("#newimpscad_mod").val();
            var fornitore = $("#fornitore_mod").val();
            var cliente = $("#cliente_mod").val();
            var numfatt = $("#numfatt_mod").val();

            if (isNotEmpty(fornitore))
            {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        if (isNotEmpty(xmlhttp.responseText)) {
                            $("#scadenzesuppl_mod").html(xmlhttp.responseText);
                            controllaDettagliRegistrazione("scadenzesuppl_mod");                            
                        }
                    }
                };
                xmlhttp.open("GET", "../primanota/aggiungiNuovaScadenzaFornitoreFacade.class.php?modo=start&fornitore=" + fornitore + "&datascad_for=" + datascad + "&impscad_for=" + impscad + "&numfatt=" + numfatt, true);
                xmlhttp.send();
            } else if (isNotEmpty(cliente))
            {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        if (isNotEmpty(xmlhttp.responseText)) {
                            $("#scadenzesuppl_mod").html(xmlhttp.responseText);
                            controllaDettagliRegistrazione("scadenzesuppl_mod");                            
                        }
                    }
                };
                xmlhttp.open("GET", "../primanota/aggiungiNuovaScadenzaClienteFacade.class.php?modo=start&&cliente=" + cliente + "&datascad_cli=" + datascad + "&impscad_cli=" + impscad + "&numfatt=" + numfatt, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuova-registrazione-form").click(
        function () {

            var D_A = $("#newsegnodett_cre").val();
            var conto = $("#conti").val().replace(",", ".");
            var importo = $("#newimpdett_cre").val();
            var importoNormalizzato = importo.trim().replace(",", ".");

            if (isNotEmpty(D_A) && isNotEmpty(conto) && isNotEmpty(importo)) {                
                if (importoNormalizzato.split(".").length <= 2) {
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange =
                            function () {
                                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                                    $("#scadenzesuppl_cre_control_group").removeClass("has-error");
                                    $("#scadenzesuppl_cre_messaggio").html("");                    
                                    var sottocontiTable = xmlhttp.responseText;
                                    $("#dettagli_cre").html(sottocontiTable);
                                    controllaDettagliRegistrazione("dettagli_cre");
                                }
                            };
                    xmlhttp.open("GET", "aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
                    xmlhttp.send();

                } else {
                    $("#dettagli_cre_messaggio").html("Importo non valido");                    
                }                
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-registrazione-form").click(
        function () {

            var D_A = $("#newsegnodett_mod").val();
            var conto = $("#conti_mod").val().replace(",", ".");
            var importo = $("#newimpdett_mod").val();
            var importoNormalizzato = importo.trim().replace(",", ".");

            if (isNotEmpty(D_A) && isNotEmpty(conto) && isNotEmpty(importo)) {                
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange =
                        function () {
                            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                                $("#scadenzesuppl_mod_control_group").removeClass("has-error");
                                $("#scadenzesuppl_mod_messaggio").html("");                    
                                var sottocontiTable = xmlhttp.responseText;
                                $("#dettagli_mod").html(sottocontiTable);
                                controllaDettagliRegistrazione("dettagli_mod");
                            }
                        };
                xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
                xmlhttp.send();                
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-cancella-registrazione-form").click(
        function () {
            $("#testo-messaggio-successo").html("Registrazione cancellata!");
            $("#messaggio-successo-dialog").modal("show");
            sleep(2000);
            $("#cancellaRegistrazioneForm").submit();
        }
);

//---------------------------------------------------------------------

function modificaRegistrazione(idRegistrazione)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            document.getElementById("modificaRegistrazioneForm").reset();

            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("registrazione").each(
                    function () {

                        $("#datareg_mod_control_group").removeClass("has-error");
                        $("#descreg_mod_control_group").removeClass("has-error");
                        $("#causale_mod_control_group").removeClass("has-error");
                        $("#codneg_mod_control_group").removeClass("has-error");
                        $("#fornitore_mod_control_group").removeClass("has-error");
                        $("#cliente_mod_control_group").removeClass("has-error");
                        $("#numfatt_mod_control_group").removeClass("has-error");
                        $("#descreg_mod_messaggio").html("");
                        $("#causale_mod_messaggio").html("");
                        $("#numfatt_mod_messaggio").html("");
                        $("#dettagli_mod_control_group").removeClass("has-error");
                        $("#dettagli_mod_messaggio").html("");

                        $("#datareg_mod").val($(this).find("datareg").text());
                        $("#descreg_mod").val($(this).find("descreg").text());

                        var causale = $(this).find("causale").text();
                        if (isEmpty(causale))
                            causale = " ";
                        
                        $("#causale_mod").selectpicker('val', causale);

                        var negozio = $(this).find("codneg").text();
                        $("#codneg_mod").selectpicker('val', negozio);

                        var fornitore = $(this).find("idfornitore").text();
                        if (isEmpty(fornitore))
                            fornitore = " ";

                        var cliente = $(this).find("idcliente").text();
                        if (isEmpty(cliente))
                            cliente = " ";

                        $("#fornitore_mod").selectpicker('val', fornitore);
                        $("#cliente_mod").selectpicker('val', cliente);
                        $("#numfatt_mod").val($(this).find("numfatt").text());
                        $("#numfatt_mod_orig").val($(this).find("numfattorig").text());

                        if (isNotEmpty(fornitore))
                            $("#scadenzesuppl_mod").html($(this).find("scadenzesupplfornitore").text());
                        if (isNotEmpty(cliente))
                            $("#scadenzesuppl_mod").html($(this).find("scadenzesupplcliente").text());

                        $("#dettagli_mod").html($(this).find("dettagli").text());
                        $("#conti_mod").html($(this).find("conti").text());
                        
                        $('#conti_mod').selectpicker('refresh');
                    }
            );
            $("#modifica-registrazione-dialog").modal("show");
        }
    };
    xmlhttp.open("POST", "../primanota/modificaRegistrazioneFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function visualizzaRegistrazione(idRegistrazione)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("registrazione").each(
                    function () {

                        $("#datareg_vis").html($(this).find("datareg").text());
                        $("#descreg_vis").html($(this).find("descreg").text());
                        $("#causale_vis").html($(this).find("causale").text());
                        $("#codneg_vis").html($(this).find("codneg").text());

                        var fornitore = $(this).find("fornitore").text();
                        var cliente = $(this).find("cliente").text();

                        $("#fornitore_vis").html(fornitore);
                        $("#cliente_vis").html(cliente);

                        $("#numfatt_vis").html($(this).find("numfatt").text());

                        if (isNotEmpty(fornitore)) {
                            $("#scadenzesuppl_vis").html($(this).find("scadenzesupplfornitore").text());
                            $("#fornitore_vis_label").show();
                            $("#cliente_vis_label").hide();
                        }
                        if (isNotEmpty(cliente)) {
                            $("#scadenzesuppl_vis").html($(this).find("scadenzesupplcliente").text());
                            $("#cliente_vis_label").show();
                            $("#fornitore_vis_label").hide();
                        }

                        $("#dettagli_vis").html($(this).find("dettagli").text());
                    }
            );
            $("#visualizza-registrazione-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
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

    if (isNotEmpty($("#datareg_cre").val())) {
        controllaDataRegistrazione("datareg_cre");
        if (isEmpty($("#datareg_cre_messaggio").text()))
            esito += "1";
        else
            esito += "0";        
    }
    else {
        esito += "0";        
        $("#datareg_cre_messaggio").html("Obbligatorio");
        $("#datareg_cre_control_group").addClass("has-error");
    }

    controllaCodice("codneg_cre");
    if (isEmpty($("#codneg_cre_messaggio").text()))
        esito += "1";
    else
        esito += "0";

    if (controllaDescrizione("descreg_cre")) {
        esito += "1";
    } else {
        esito += "0";
    }

    if (isNotEmpty($("#fornitore_cre").val()) || isNotEmpty($("#cliente_cre").val())) {
        controllaDettagliRegistrazione("dettagli_cre");
        if (isEmpty($("#dettagli_cre_messaggio").text()))
            esito += "1";
        else
            esito += "0";
    }
    else {
        if (isEmpty($("#fornitore_cre").val()) && isEmpty($("#cliente_cre").val())) {
            esito += "1";            
        } else {
            esito += "0";        
            $("#dettagli_cre_messaggio").html("Obbligatorio");
            $("#dettagli_cre_control_group").addClass("has-error");                    
        }        
    }

    if (isNotEmpty($("#causale_cre").val())) {
        if (controllaCausale("causale_cre")) {
            esito += "1";
        } else {
            esito += "0";
        }
    }
    else {
        esito += "0";        
        $("#causale_cre_messaggio").html("Obbligatorio");
        $("#causale_cre_control_group").addClass("has-error");        
    }

    if (controllaClienteFornitore("fornitore_cre", "cliente_cre")) {
        esito += "1";
        
        if (isNotEmpty($("#fornitore_cre").val()) || isNotEmpty($("#cliente_cre").val())) {
            validaNumeroFattura("cliente_cre", "fornitore_cre", "numfatt_cre", "datareg_cre");
            if (isEmpty($("#numfatt_cre_messaggio").text())) {
                esito += "1";
                if (controllaNumeroFattura("numfatt_cre")) {
                    esito += "1";
                }
                else {
                    esito += "0";
                }
            }
            else {
                esito += "00";
            }                        
        }
        else
            esito += "11";            
    }
    else {
        esito += "00";
    }

    controllaRegistrazioneDoppia("datareg_cre", "causale_cre", "descreg_cre", "dettagli_cre");
    if (isEmpty($("#dettagli_cre_messaggio").text())) {
        esito += "1";
    } else {
        esito += "0";
    }

    if (esito === "111111111") {
        return true;
    } else {
        return false;
    }
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

    if (isNotEmpty($("#datareg_mod").val())) {
        controllaDataRegistrazione("datareg_mod");
        if (isEmpty($("#datareg_mod_messaggio").text()))
            esito += "1";
        else
            esito += "0";        
    }
    else {
        esito += "0";        
        $("#datareg_mod_messaggio").html("Obbligatorio");
        $("#datareg_mod_control_group").addClass("has-error");
    }
    
    controllaCodice("codneg_mod");
    if (isEmpty($("#codneg_mod_messaggio").text()))
        esito += "1";
    else
        esito += "0";

    if (controllaDescrizione("descreg_mod")) {
        esito += "1";
    } else {
        esito += "0";
    }

    if (isEmpty($("#dettagli_mod_messaggio").text()))
        esito += "1";
    else
        esito += "0";

    if (controllaCausale("causale_mod")) {
        esito += "1";
    } else {
        esito += "0";
    }

    if (controllaClienteFornitore("fornitore_mod", "cliente_mod")) {
        esito += "1";
        
        if (isNotEmpty($("#fornitore_mod").val()) || isNotEmpty($("#cliente_mod").val())) {
            if (controllaNumeroFattura("numfatt_mod"))
                esito += "1";
            else
                esito += "0";            
        }
        else
            esito += "1";            
    }
    else {
        esito += "00";
    }

    if (esito === "1111111") {
        return true;
    } else {
        return false;
    }
}

// ---------------------------------------------------------------------
// Function di aggiornamento
// ---------------------------------------------------------------------

function modificaDettaglioRegistrazione(idTable, idTableScad, conto, sottoconto, importoField, segnoField, idDettaglio)
{
    var importoDettNormalizzato;
    var importo = $("#" + importoField).val();
    var segno = $("#" + segnoField).val();    
    
    if (isEmpty(importo))
        importoDettNormalizzato = 0;
    else
        importoDettNormalizzato = importo.trim().replace(",", ".");

    if (isNotEmpty(conto)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    $("#" + idTable + "_control_group").removeClass("has-error");
                    $("#" + idTable + "_messaggio").html("");                    
                    $("#" + idTableScad + "_control_group").removeClass("has-error");
                    $("#" + idTableScad + "_messaggio").html("");       
                    var dettagliTable = xmlhttp.responseText;
                    $("#" + idTable).html(dettagliTable);
                    controllaDettagliRegistrazione(idTable);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&importo=" + importoDettNormalizzato + "&dareAvere=" + segno + "&iddettaglio=" + idDettaglio, true);
        xmlhttp.send();
    }   
}

// ---------------------------------------------------------------------
// Function di aggiornamento
// ---------------------------------------------------------------------

function modificaSegnoDettaglioRegistrazione(idTable, idTableScad, conto, sottoconto, importoField, segnoField, idDettaglio)
{
    var importoDettNormalizzato;
    var importo = $("#" + importoField).val();
    var segno = $("#" + segnoField).val();    
    
    if (isEmpty(importo))
        importoDettNormalizzato = 0;
    else
        importoDettNormalizzato = importo.trim().replace(",", ".");

    if (isNotEmpty(conto)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    $("#" + idTable + "_control_group").removeClass("has-error");
                    $("#" + idTable + "_messaggio").html("");                    
                    $("#" + idTableScad + "_control_group").removeClass("has-error");
                    $("#" + idTableScad + "_messaggio").html("");       
                    var dettagliTable = xmlhttp.responseText;
                    $("#" + idTable).html(dettagliTable);
                    controllaDettagliRegistrazione(idTable);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaSegnoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&importo=" + importoDettNormalizzato + "&dareAvere=" + segno + "&iddettaglio=" + idDettaglio, true);
        xmlhttp.send();
    }   
}

//---------------------------------------------------------------------

function aggiornaTabellaDettaglioRegistrazione() {

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "../primanota/aggiornaTabellaDettaglioRegistrazioneFacade.class.php?modo=go", true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function ripartisciImportoSuScadenzeFornitore(importo) {
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            if (isNotEmpty(xmlhttp.responseText)) {
                var scadenzeTable = xmlhttp.responseText;
                $("#scadenzesuppl_cre").html(scadenzeTable);
            }
        }
    };
    xmlhttp.open("GET", "../primanota/scadenziaImportoDettaglioRegistrazioneFornitoreFacade.class.php?modo=go&importo_dettaglio=" + importo, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function ripartisciImportoSuScadenzeCliente(importo) {
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            if (isNotEmpty(xmlhttp.responseText)) {
                var scadenzeTable = xmlhttp.responseText;
                $("#scadenzesuppl_cre").html(scadenzeTable);
            }
        }
    };
    xmlhttp.open("GET", "../primanota/scadenziaImportoDettaglioRegistrazioneClienteFacade.class.php?modo=go&importo_dettaglio=" + importo, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function aggiungiDettaglioContoFornitore(idfornitore, campoDett, campoCausale)
{
    if (isNotEmpty(idfornitore))
    {
        if (isNotEmpty(campoCausale)) {
            var causale = $("#" + campoCausale).val();
        } else {
            var causale = "";
        }
        
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                $("#" + campoDett + "_control_group").removeClass("has-error");
                $("#" + campoDett + "_messaggio").html("");                    
                if (isNotEmpty(xmlhttp.responseText)) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#" + campoDett).html(dettagliTable);
                    $("#" + campoDett).show();
                    controllaDettagliRegistrazione(campoDett);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioContoFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore + "&codCausale=" + causale, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------

function aggiungiDettaglioContoCliente(idcliente, campoDett)
{
    if (isNotEmpty(idcliente))
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                $("#" + campoDett + "_control_group").removeClass("has-error");
                $("#" + campoDett + "_messaggio").html("");                    
                if (isNotEmpty(xmlhttp.responseText)) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#" + campoDett).html(dettagliTable);
                    $("#" + campoDett).show();
                    controllaDettagliRegistrazione(campoDett);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioContoClienteFacade.class.php?modo=go&idcliente=" + idcliente, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

function cancellaNuovaScadenzaFornitore(idTable, idFornitore, datScad, numFatt)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            $("#" + idTable).html(xmlhttp.responseText);
            $("#" + idTable + "_control_group").removeClass("has-error");
            $("#" + idTable + "_messaggio").html("");                    
            controllaDettagliRegistrazione(idTable);
  }
    };
    xmlhttp.open("GET", "../primanota/cancellaScadenzaFornitoreFacade.class.php?modo=start&idfornitore=" + idFornitore + "&datascad_for=" + datScad + "&numfatt=" + numFatt, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function cancellaNuovaScadenzaCliente(idTable, idCliente, datScad, numFatt)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            $("#" + idTable).html(xmlhttp.responseText);
            $("#" + idTable + "_control_group").removeClass("has-error");
            $("#" + idTable + "_messaggio").html("");                    
            controllaDettagliRegistrazione(idTable);
        }
    };
    xmlhttp.open("GET", "../primanota/cancellaScadenzaClienteFacade.class.php?modo=start&idcliente=" + idCliente + "&datascad_cli=" + datScad + "&numfatt=" + numFatt, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function modificaImportoScadenzaFornitore(idTable, idfornitore, datascad, numfatt, importo, idTableDet)
{
    var importoScadNormalizzato;
        
    if (isEmpty(importo))
        importoScadNormalizzato = 0;
    else
        importoScadNormalizzato = importo.trim().replace(",", ".");

    if (isNotEmpty(datascad)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                $("#" + idTable + "_control_group").removeClass("has-error");
                $("#" + idTable + "_messaggio").html("");                    
                if (isNotEmpty(xmlhttp.responseText)) {
                    var scadenzeTable = xmlhttp.responseText;
                    $("#" + idTable).html(scadenzeTable);
                    controllaDettagliRegistrazione(idTable);                    
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaImportoScadenzaFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore + "&datascad_for=" + datascad + "&numfatt=" + numfatt + "&impscad_for=" + importoScadNormalizzato, true);
        xmlhttp.send();
    }
}

function modificaDataScadenzaFornitore(idTable, idfornitore, datascad_old, datascad_new, numfatt) {
    
    if (isNotEmpty(datascad_old)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    var scadenzeTable = xmlhttp.responseText;
                    $("#" + idTable).html(scadenzeTable);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaDataScadenzaFornitoreFacade.class.php?modo=go&idfornitore=" + idfornitore + "&datascad_old_for=" + datascad_old  + "&datascad_new_for=" + datascad_new + "&numfatt=" + numfatt, true);
        xmlhttp.send();
    }
}

function modificaDataScadenzaCliente(idTable, idcliente, datascad_old, datascad_new, numfatt) {
    
    if (isNotEmpty(datascad_old)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    var scadenzeTable = xmlhttp.responseText;
                    $("#" + idTable).html(scadenzeTable);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaDataScadenzaClienteFacade.class.php?modo=go&idcliente=" + idcliente + "&datascad_old_cli=" + datascad_old  + "&datascad_new_cli=" + datascad_new + "&numfatt=" + numfatt, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

function modificaImportoScadenzaCliente(idTable, idcliente, datascad, numfatt, importo, idTableDet)
{
    var importoScadNormalizzato;
    
    if (isEmpty(importo))
        importoScadNormalizzato = 0;
    else
        importoScadNormalizzato = importo.trim().replace(",", ".");

    if (isNotEmpty(datascad)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    $("#" + idTable + "_control_group").removeClass("has-error");
                    $("#" + idTable + "_messaggio").html("");                    
                    var scadenzeTable = xmlhttp.responseText;
                    $("#" + idTable).html(scadenzeTable);
                    controllaDettagliRegistrazione(idTable);                    
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaImportoScadenzaClienteFacade.class.php?modo=go&idcliente=" + idcliente + "&datascad_cli=" + datascad + "&numfatt=" + numfatt + "&impscad_cli=" + importoScadNormalizzato, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

function cancellaDettaglioNuovaRegistrazione(idTable, codContoComposto, segno)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            var dettagliTable = xmlhttp.responseText;
            $("#" + idTable).html(dettagliTable);
            controllaDettagliRegistrazione(idTable);
        }
    };
    xmlhttp.open("GET", "../primanota/cancellaNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + codContoComposto + "&dareAvere=" + segno, true);
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
        function () {

            var idfornitore = $("#fornitore_cre").val();
            var datareg = $("#datareg_cre").val();

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    if (isNotEmpty(xmlhttp.responseText)) {
                        $("#datascad_cre_label").show();
                        $("#scadenzesuppl_cre").html(xmlhttp.responseText);
                    }
                }
            };
            xmlhttp.open("GET", "../primanota/calcolaDataScadenzaFornitoreFacade.class.php?modo=start&idfornitore=" + idfornitore + "&datareg_cre=" + datareg, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#fornitore_mod").change(
        function () {

            var desfornitore = $("#fornitore_mod").val();
            var datareg = $("#datareg_mod").val();

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    if (isNotEmpty(xmlhttp.responseText)) {
                        $("#scadenzesuppl_mod").html(xmlhttp.responseText);
                    }
                }
            };
            xmlhttp.open("GET", "../primanota/calcolaDataScadenzaFornitoreFacade.class.php?modo=start&desfornitore=" + desfornitore + "&datareg_mod=" + datareg, true);
            xmlhttp.send();
        }
);

// ---------------------------------------------------------------------------------

$("#cliente_cre").change(
        function () {

            var descliente = $("#cliente_cre").val();
            var datareg = $("#datareg_cre").val();

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    if (isNotEmpty(xmlhttp.responseText)) {
                        $("#datascad_cre_label").show();
                        $("#scadenzesuppl_cre").html(xmlhttp.responseText);
                    }
                }
            };
            xmlhttp.open("GET", "../primanota/calcolaDataScadenzaClienteFacade.class.php?modo=start&descliente=" + descliente + "&datareg=" + datareg, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#cliente_mod").change(
        function () {

            var descliente = $("#cliente_mod").val();
            var datareg = $("#datareg_mod").val();

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    if (isNotEmpty(xmlhttp.responseText)) {
                        $("#scadenzesuppl_mod").html(xmlhttp.responseText);
                    }
                }
            };
            xmlhttp.open("GET", "../primanota/calcolaDataScadenzaClienteFacade.class.php?modo=start&descliente=" + descliente + "&datareg=" + datareg, true);
            xmlhttp.send();
        }
);

// ---------------------------------------------------------------------------------

$("#causale_cre").change(
        function () {
            var causale = $("#causale_cre").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti").html(xmlhttp.responseText);
                        $('#conti').selectpicker('refresh');
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_mod").change(
        function () {
            var causale = $("#causale_mod").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_mod").html(xmlhttp.responseText);
                        $("#conti_mod").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_inc").change(
        function () {
            var causale = $("#causale_inc").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_inc").html(xmlhttp.responseText);
                        $("#conti_inc").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_inc_mod").change(
        function () {
            var causale = $("#causale_inc_mod").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_inc_mod").html(xmlhttp.responseText);
                        $("#conti_inc_mod").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_pag_cre").change(
        function () {
            var causale = $("#causale_pag_cre").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_pag").html(xmlhttp.responseText);
                        $("#conti_pag").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_pag_mod").change(
        function () {
            var causale = $("#causale_pag_mod").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_pag_mod").html(xmlhttp.responseText);
                        $("#conti_pag_mod").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_cormer_cre").change(
        function () {
            var causale = $("#causale_cormer_cre").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_cormer_cre").html(xmlhttp.responseText);
                        $("#conti_cormer_cre").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_cormer_mod").change(
        function () {
            var causale = $("#causale_cormer_mod").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_cormer_mod").html(xmlhttp.responseText);
                        $("#conti_cormer_mod").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_corneg_cre").change(
        function () {
            var causale = $("#causale_corneg_cre").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_corneg_cre").html(xmlhttp.responseText);
                        $("#conti_corneg_cre").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_corneg_mod").change(
        function () {
            var causale = $("#causale_corneg_mod").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_corneg_mod").html(xmlhttp.responseText);
                        $("#conti_corneg_mod").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "../primanota/loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

// ---------------------------------------------------------------------------------