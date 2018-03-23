//---------------------------------------------------------------------------------
// Incassi
//---------------------------------------------------------------------------------

$("#nuovo-incasso").click(function (event) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            document.getElementById("nuovoIncassoForm").reset();
            $("#codneg_inc_cre").selectpicker('val', ' ');
            $("#causale_inc_cre").selectpicker('val', ' ');
            $("#cliente_inc_cre").selectpicker('val', ' ');
            $("#scadenze_aperte_inc_cre").html("");
            $("#scadenze_chiuse_inc_cre").html("");
            $("#dettagli_inc_cre").html("");
            $("#dettagli_inc_cre_messaggio").html("");
            $("#nuovo-incasso-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "creaIncassoFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#cliente_inc_cre").keyup(function () {

    var descliente = $("#cliente_inc_cre").val();
    var codnegozio = $("#codneg_inc_cre").val();

    if (descliente !== "") {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {

                var parser = new DOMParser();
                var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

                $(xmldoc).find("scadenzecliente").each(
                        function () {
                            $("#scadenze_chiuse_inc_cre").html($(this).find("scadenzeincassate").text());
                            $("#scadenze_aperte_inc_cre").html($(this).find("scadenzedaincassare").text());
                        }
                );
            }
        };
        xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente_inc_cre=" + descliente + "&codnegozio_inc_cre=" + codnegozio, true);
        xmlhttp.send();
    }
});

//---------------------------------------------------------------------------------

$("#cliente_inc_mod").keyup(function () {

    var descliente = $("#cliente_inc_mod").val();
    var codnegozio = $("#codneg_inc_mod").val();

    if (descliente !== "") {
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
        xmlhttp.open("GET", "ricercaScadenzeAperteClienteFacade.class.php?modo=start&descliente_inc_mod=" + descliente + "&codnegozio_inc_mod=" + codnegozio, true);
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
                    controllaDettagliRegistrazione("dettagli_inc_cre");
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

            var conto = $("#conti_inc-mod").val().replace(",", ".");
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

            if (causale !== "") {
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

            if (causale !== "") {
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
                sleep(3000);
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
                sleep(3000);
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

    controllaDataRegistrazione("datareg_inc_" + type);
    if ($("#datareg_inc_" + type + "_messaggio").text() === "")
        esito = esito + "1";
    else
        esito = esito + "0";

    if ($("#descreg_inc_" + type).val() !== "") {
        if (controllaDescrizione("descreg_inc_" + type))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if ($("#causale_inc_" + type).val() !== "") {
        controllaDettagliRegistrazione("dettagli_inc_" + type);
        if ($("#dettagli_inc_" + type + "_messaggio").text() === "")
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if (esito === "111") {
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
                    }
            );
            $("#modifica-incasso-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../primanota/modificaIncassoFacade.class.php?modo=start&idinc=" + idIncasso, true);
    xmlhttp.send();
}
