//---------------------------------------------------------------------------------
// Pagamenti
//---------------------------------------------------------------------------------

$("#nuovo-pagamento").click(function (event) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200)) {
            document.getElementById("nuovoPagamentoForm").reset();
            $("#codneg_pag_cre").selectpicker('val', ' ');
            $("#causale_pag_cre").selectpicker('val', ' ');
            $("#fornitore_pag_cre").selectpicker('val', ' ');
            $("#scadenze_aperte_pag_cre").html("");
            $("#scadenze_chiuse_pag_cre").html("");
            $("#dettagli_pag_cre").html("");
            $("#dettagli_pag_cre_messaggio").html("");
            $("#nuovo-pagamento-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "creaPagamentoFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

function trovaScadenzeFornitore() {
	
    var idfornitore = $("#fornitore_pag_cre").val();
    var codnegozio = $("#codneg_pag_cre").val();

    if (idfornitore !== "") {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {

                var parser = new DOMParser();
                var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

                $(xmldoc).find("scadenzefornitore").each(
                        function () {
                            $("#scadenze_chiuse_pag_cre").html($(this).find("scadenzepagate").text());
                            $("#scadenze_aperte_pag_cre").html($(this).find("scadenzedapagare").text());
                        }
                );
            }
        };
        xmlhttp.open("GET", "ricercaScadenzeAperteFornitoreFacade.class.php?modo=start&fornitore_pag_cre=" + idfornitore + "&codnegozio_pag_cre=" + codnegozio, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuovo-pagamento-form").click(function () {
    $("#nuovo-dettaglio-pagamento-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-pagamento-form").click(function () {
    $("#nuovo-dettaglio-modifica-pagamento-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuovo-pagamento-form").click(
        function () {

            var D_A = $("#newsegnodett_pag_cre").val();

            // tolgo eventuali virgole nella descrizione del conto

            var conto = $("#conti_pag").val().replace(",", ".");
            var idconto = conto.substring(0, 6);

            // normalizzo la virgola dell'importo

            var importo = $("#newimpdett_pag_cre").val();
            var importoNormalizzato = importo.trim().replace(",", ".");

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    var sottocontiTable = xmlhttp.responseText;
                    $("#dettagli_pag_cre").html(sottocontiTable);
                    controllaDettagliRegistrazione("dettagli_pag_cre");
                }
            };
            xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-pagamento-form").click(
        function () {

            var D_A = $("#newsegnodett_pag_mod").val();

            // tolgo eventuali virgole nella descrizione del conto

            var conto = $("#conti_pag_mod").val().replace(",", ".");
            var idconto = conto.substring(0, 6);

            // normalizzo la virgola dell'importo

            var importo = $("#newimpdett_pag_mod").val();
            var importoNormalizzato = importo.trim().replace(",", ".");

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    var sottocontiTable = xmlhttp.responseText;
                    $("#dettagli_pag_mod").html(sottocontiTable);
                    controllaDettagliRegistrazione("dettagli_pag_mod");
                }
            };
            xmlhttp.open("GET", "../primanota/aggiungiNuovoDettaglioRegistrazioneFacade.class.php?modo=go&codconto=" + conto + "&dareAvere=" + D_A + "&importo=" + importoNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

function aggiungiFatturaPagata(idScadenza, idTableAperte, idTableChiuse)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenzefornitore").each(
                    function () {
                        $("#" + idTableChiuse).html($(this).find("scadenzepagate").text());
                        $("#" + idTableAperte).html($(this).find("scadenzedapagare").text());
                    }
            );
        }
    };
    xmlhttp.open("GET", "../primanota/aggiungiFatturaPagataFacade.class.php?modo=start&idscadfor=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function rimuoviFatturaPagata(idScadenza, idTableAperte, idTableChiuse)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenzefornitore").each(
                    function () {
                        $("#" + idTableChiuse).html($(this).find("scadenzepagate").text());
                        $("#" + idTableAperte).html($(this).find("scadenzedapagare").text());
                    }
            );
        }
    };
    xmlhttp.open("GET", "../primanota/rimuoviFatturaPagataFacade.class.php?modo=start&idscadfor=" + idScadenza + "&idtableaperte=" + idTableAperte + "&idtablechiuse=" + idTableChiuse, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#causale_pag_cre").change(
        function () {
            var causale = $("#causale_pag_cre").val();

            if (causale !== "") {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_pag").html(xmlhttp.responseText);
//                        $("#conti_pag").selectmenu("refresh");
                    }
                };
                xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#causale_pag_mod").change(
        function () {
            var causale = $("#causale_pag_mod").val();

            if (causale !== "") {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        $("#conti_pag_mod").html(xmlhttp.responseText);
                        $("#conti_pag_mod").selectmenu("refresh");
                    }
                };
                xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
                xmlhttp.send();
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-pagamento-form").click(
        function () {
            if (validaPagamento("cre")) {
                $("#testo-messaggio-successo").html("Pagamento salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                sleep(3000);
                $("#nuovoPagamentoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il pagamento non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------
// VALIDAZIONE FORM PAGAMENTO
//---------------------------------------------------------------------------------

function validaPagamento(type)
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
     * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
     */
    var esito = "";

    controllaDataRegistrazione("datareg_pag_" + type);
    if ($("#datareg_pag_" + type + "_messaggio").text() === "")
        esito = esito + "1";
    else
        esito = esito + "0";

    if ($("#descreg_pag_" + type).val() !== "") {
        if (controllaDescrizione("descreg_pag_" + type))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if ($("#causale_pag_" + type).val() !== "") {
        controllaDettagliRegistrazione("dettagli_pag_" + type);
        if ($("#dettagli_pag_" + type + "_messaggio").text() === "")
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

function visualizzaPagamento(idPagamento)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("pagamento").each(
                    function () {

                        $("#datareg_pag_vis").html($(this).find("datareg").text());
                        $("#descreg_pag_vis").html($(this).find("descreg").text());
                        $("#causale_pag_vis").html($(this).find("causale").text());
                        $("#codneg_pag_vis").html($(this).find("codneg").text());

                        var fornitore = $(this).find("fornitore").text();

                        $("#fornitore_pag_vis").html(fornitore);
                        $("#scadenze_pagate_pag_vis").html($(this).find("scadenzepagate").text());
                        $("#dettagli_pag_vis").html($(this).find("dettagli").text());
                    }
            );
            $("#visualizza-pagamento-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../primanota/visualizzaPagamentoFacade.class.php?modo=start&idpag=" + idPagamento, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function modificaPagamento(idPagamento)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("pagamento").each(
                    function () {

                        $("#datareg_pag_mod").val($(this).find("datareg").text());
                        $("#descreg_pag_mod").val($(this).find("descreg").text());

                        var causale = $(this).find("causale").text();
                        $("#causale_pag_mod").selectpicker('val', causale);

                        var negozio = $(this).find("codneg").text();
                        $("#codneg_pag_mod").selectpicker('val', negozio);

                        var fornitore = $(this).find("fornitore").text();
                        $("#fornitore_pag_mod").selectpicker('val', fornitore);

                        $("#scadenze_chiuse_pag_mod").html($(this).find("scadenzepagate").text());
                        $("#scadenze_aperte_pag_mod").html($(this).find("scadenzedapagare").text());
                        $("#dettagli_pag_mod").html($(this).find("dettagli").text());
                        $("#conti_pag_mod").html($(this).find("conti").text());
                    }
            );
            $("#modifica-pagamento-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../primanota/modificaPagamentoFacade.class.php?modo=start&idpag=" + idPagamento, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#button-ok-modifica-pagamento-form").click(
        function () {
            if (validaPagamento("mod")) {
                $("#testo-messaggio-successo").html("Pagamento salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                sleep(3000);
                $("#modificaPagamentoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il pagamento non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

