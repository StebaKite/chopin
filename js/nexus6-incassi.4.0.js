//---------------------------------------------------------------------------------
// Incassi
//---------------------------------------------------------------------------------

$("#nuovo-incasso").click(function (event) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            
            // pulizia degli elementi in pagina
            document.getElementById("nuovoIncassoForm").reset();
            
            $("#datareg_inc_cre_control_group").removeClass("has-error");
            $("#descreg_inc_cre_control_group").removeClass("has-error");
            $("#causale_inc_cre_control_group").removeClass("has-error");
            $("#codneg_inc_cre_control_group").removeClass("has-error");
            $("#cliente_inc_cre_control_group").removeClass("has-error");
            $("#scadenze_chiuse_inc_cre_control_group").removeClass("has-error");
            $("#scadenze_aperte_inc_cre_control_group").removeClass("has-error");
            $("#dettagli_inc_cre_control_group").removeClass("has-error");

            $("#descreg_inc_cre_messaggio").html("");
            $("#causale_inc_cre_messaggio").html("");
            $("#codneg_inc_cre_messaggio").html("");
            $("#cliente_inc_cre_messaggio").html("");
            $("#scadenze_chiuse_inc_cre_messaggio").html("");
            $("#scadenze_aperte_inc_cre_messaggio").html("");
            $("#dettagli_inc_cre_messaggio").html("");
            
            $("#codneg_inc_cre").selectpicker('val', ' ');
            $("#causale_inc_cre").selectpicker('val', ' ');
            $("#cliente_inc_cre").selectpicker('val', ' ');
            $("#scadenze_aperte_inc_cre").html("");
            $("#scadenze_chiuse_inc_cre").html("");
            $("#dettagli_inc_cre").html("");
            
            // pulizia delle altre tabelle incluse nella pagina ricerca registrazioni
            $("#dettagli_cre").html("");
            $("#dettagli_mod").html("");
            $("#dettagli_pag_cre").html("");
            $("#dettagli_pag_mod").html("");
            $("#dettagli_cormer_cre").html("");
            $("#dettagli_cormer_mod").html("");
            $("#dettagli_corneg_cre").html("");
            $("#dettagli_corneg_mod").html("");
            
            // pulizia del messaggio
            $("#dettagli_inc_cre_messaggio").html("");
            $("#nuovo-incasso-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "creaIncassoFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

function trovaScadenzeCliente(idfunz) {

    var idcliente = $("#cliente_" + idfunz).val();
    var codnegozio = $("#codneg_" + idfunz).val();

    if (isNotEmpty(idcliente)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {

                var parser = new DOMParser();
                var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

                $(xmldoc).find("scadenzecliente").each(
                        function () {
                            $("#scadenze_chiuse_" + idfunz).html($(this).find("scadenzeincassate").text());
                            $("#scadenze_aperte_" + idfunz).html($(this).find("scadenzedaincassare").text());
                        }
                );
            }
        };
        xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&cliente_" + idfunz + "=" + idcliente + "&codneg_" + idfunz + "=" + codnegozio, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

$("#cliente_inc_mod").keyup(function () {

    var descliente = $("#cliente_inc_mod").val();
    var codnegozio = $("#codneg_inc_mod").val();

    if (isNotEmpty(descliente)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {

                var parser = new DOMParser();
                var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

                $(xmldoc).find("scadenzecliente").each(
                        function () {
                            $("#scadenze_chiuse_inc_mod").html($(this).find("scadenzeincassate").text());
                            $("#scadenze_aperte_inc_mod").html($(this).find("scadenzedaincassare").text());
                        }
                );
            }
        };
        xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente_inc_mod=" + descliente + "&codneg_inc_mod=" + codnegozio, true);
        xmlhttp.send();
    }
});

//---------------------------------------------------------------------------------

function aggiungiFatturaIncassata(idScadenza, idTableAperte, idTableChiuse)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenzecliente").each(
                    function () {
                        $("#" + idTableChiuse).html($(this).find("scadenzeincassate").text());
                        $("#" + idTableAperte).html($(this).find("scadenzedaincassare").text());
                    }
            );
        }
    };
    xmlhttp.open("GET", "../primanota/aggiungiFatturaIncassataFacade.class.php?modo=start&idscadcli=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function rimuoviFatturaIncassata(idScadenza, idTableAperte, idTableChiuse)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenzecliente").each(
                    function () {
                        $("#" + idTableChiuse).html($(this).find("scadenzeincassate").text());
                        $("#" + idTableAperte).html($(this).find("scadenzedaincassare").text());
                    }
            );
        }
    };
    xmlhttp.open("GET", "../primanota/rimuoviFatturaIncassateFacade.class.php?modo=start&idscadcli=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuovo-incasso-form").click(function () {
    $("#nuovo-dettaglio-incasso-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-incasso-form").click(function () {
    $("#nuovo-dettaglio-modifica-incasso-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuovo-incasso-form").click(
        function () {

            var D_A = $("#newsegnodett_inc_cre").val();

            // tolgo eventuali virgole nella descrizione del conto

            var conto = $("#conti_inc_cre").val().replace(",", ".");
            var idconto = conto.substring(0, 6);

            // normalizzo la virgola dell'importo

            var importo = $("#newimpdett_inc_cre").val();
            var importoNormalizzato = importo.trim().replace(",", ".");

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    var sottocontiTable = xmlhttp.responseText;
                    $("#dettagli_inc_cre").html(sottocontiTable);
                    controllaDettagliIncasso("dettagli_inc_cre");
                }
            };
            xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-incasso-form").click(
        function () {

            var D_A = $("#newsegnodett_inc_mod").val();

            // tolgo eventuali virgole nella descrizione del conto

            var conto = $("#conti_inc_mod").val().replace(",", ".");
            var idconto = conto.substring(0, 6);

            // normalizzo la virgola dell'importo

            var importo = $("#newimpdett_inc_mod").val();
            var importoNormalizzato = importo.trim().replace(",", ".");

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    var sottocontiTable = xmlhttp.responseText;
                    $("#dettagli_inc_mod").html(sottocontiTable);
                    controllaDettagliRegistrazione("dettagli_inc_mod");
                }
            };
            xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#causale_inc_cre").change(
        function () {
            var causale = $("#causale_inc_cre").val();

            if (isNotEmpty(causale)) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_inc_cre").html(xmlhttp.responseText);
                        $("#conti_inc_cre").selectpicker("refresh");
                    }
                };
                xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
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
                xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-incasso-form").click(
        function () {
            if (validaIncasso("cre")) {
                $("#testo-messaggio-successo").html("Incasso salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("nuovo-incasso-dialog").modal("hide");
                $("#nuovoIncassoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore l'incasso non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-incasso-form").click(
        function () {
            if (validaIncasso("mod")) {
                $("#testo-messaggio-successo").html("Incasso salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("#modifica-incasso-dialog").modal("hide");
                $("#modificaIncassoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore l'incasso non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------
// VALIDAZIONE FORM INCASSO
//---------------------------------------------------------------------------------

function validaIncasso(type)
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
     * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
     */
    var esito = "";

    if (isNotEmpty($("#datareg_inc_" + type).val())) {
        controllaDataRegistrazione("datareg_inc_" + type);
        if (isEmpty($("#datareg_inc_" + type + "_messaggio").text()))
            esito = esito + "1";
        else
            esito = esito + "0";        
    }
    else {
        esito += "0";        
        $("#datareg_inc_" + type + "_messaggio").html("Obbligatorio");
        $("#datareg_inc_" + type + "_control_group").addClass("has-error");        
    }

    controllaCodice("codneg_inc_" +  type);
    if (isEmpty($("#codneg_inc_" + type + "_messaggio").text()))
        esito += "1";
    else
        esito += "0";

    if (controllaDescrizione("descreg_inc_" + type))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (isNotEmpty($("#cliente_inc_" + type).val())) {
        controllaDettagliRegistrazione("dettagli_inc_" + type);
        if (isEmpty($("#dettagli_inc_" + type + "_messaggio").text()))
            esito = esito + "1";
        else
            esito = esito + "0";
    }
    else {
        esito += "0";        
        $("#cliente_inc_" + type + "_messaggio").html("Obbligatorio");
        $("#cliente_inc_" + type + "_control_group").addClass("has-error");   
        if (isEmpty($("#dettagli_inc_" + type).text())) {
            $("#dettagli_inc_" + type + "_messaggio").html("Obbligatorio");
            $("#dettagli_inc_" + type + "_control_group").addClass("has-error");                    
        }
    }

    if (isNotEmpty($("#causale_inc_" + type).val())) {
        if (controllaCausale("causale_inc_" + type)) {
            esito += "1";
        } else {
            esito += "0";
        }
    }
    else {
        esito += "0";        
        $("#causale_inc_" + type + "_messaggio").html("Obbligatorio");
        $("#causale_inc_" + type + "_control_group").addClass("has-error");        
    }

    if (esito === "11111") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------

function visualizzaIncasso(idIncasso)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("incasso").each(
                    function () {

                        $("#datareg_inc_vis").html($(this).find("datareg").text());
                        $("#descreg_inc_vis").html($(this).find("descreg").text());
                        $("#causale_inc_vis").html($(this).find("causale").text());
                        $("#codneg_inc_vis").html($(this).find("codneg").text());

                        var cliente = $(this).find("cliente").text();

                        $("#cliente_inc_vis").html(cliente);
                        $("#scadenze_incassate_inc_vis").html($(this).find("scadenzeincassate").text());
                        $("#dettagli_inc_vis").html($(this).find("dettagli").text());
                    }
            );
            $("#visualizza-incasso-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../primanota/visualizzaIncassoFacade.class.php?modo=start&idinc=" + idIncasso, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function modificaIncasso(idIncasso)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("incasso").each(
                    function () {

                        $("#datareg_inc_mod_control_group").removeClass("has-error");
                        $("#descreg_inc_mod_control_group").removeClass("has-error");
                        $("#causale_inc_mod_control_group").removeClass("has-error");
                        $("#codneg_inc_mod_control_group").removeClass("has-error");
                        $("#cliente_inc_mod_control_group").removeClass("has-error");
                        $("#scadenze_chiuse_inc_mod_control_group").removeClass("has-error");
                        $("#scadenze_aperte_inc_mod_control_group").removeClass("has-error");
                        $("#dettagli_inc_mod_control_group").removeClass("has-error");

                        $("#descreg_inc_mod_messaggio").html("");
                        $("#causale_inc_mod_messaggio").html("");
                        $("#codneg_inc_mod_messaggio").html("");
                        $("#cliente_inc_mod_messaggio").html("");
                        $("#scadenze_chiuse_inc_mod_messaggio").html("");
                        $("#scadenze_aperte_inc_mod_messaggio").html("");
                        $("#dettagli_inc_mod_messaggio").html("");

                        $("#datareg_inc_mod").val($(this).find("datareg").text());
                        $("#descreg_inc_mod").val($(this).find("descreg").text());

                        var causale = $(this).find("causale").text();
                        $("#causale_inc_mod").selectpicker('val', causale);

                        var negozio = $(this).find("codneg").text();
                        $("#codneg_inc_mod").selectpicker('val', negozio);

                        var cliente = $(this).find("cliente").text();
                        $("#cliente_inc_mod").selectpicker('val', cliente);

                        $("#scadenze_chiuse_inc_mod").html($(this).find("scadenzeincassate").text());
                        $("#scadenze_aperte_inc_mod").html($(this).find("scadenzedaincassare").text());
                        $("#dettagli_inc_mod").html($(this).find("dettagli").text());
                        $("#conti_inc_mod").html($(this).find("conti").text());
                        $("#conti_inc_mod").selectpicker('refresh');
                    }
            );
            $("#modifica-incasso-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../primanota/modificaIncassoFacade.class.php?modo=start&idinc=" + idIncasso, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function controllaDettagliIncasso(campoDet)
{
    /**
     * I dettagli dell'incasso devono essere presenti e gli importi del
     * Dare e Avere sui vari conti devono annularsi.
     * L'importo inserito sul conto principale (cliente) deve quadrare con la
     * somma degli importi delle scadenze incassate
     */
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            if (isNotEmpty(xmlhttp.responseText)) {
                $("#" + campoDet + "_control_group").addClass("has-error");
                $("#" + campoDet + "_messaggio").html(xmlhttp.responseText);
            } else {
                $("#" + campoDet + "_control_group").removeClass("has-error");
                $("#" + campoDet + "_messaggio").html("");
                aggiornaTabellaDettaglioRegistrazione();
            }
        }
    };
    xmlhttp.open("GET", "../primanota/verificaDettagliIncassoFacade.class.php?modo=start", true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------
