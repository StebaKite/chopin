//---------------------------------------------------------------------------------
// Scadenze
//---------------------------------------------------------------------------------

function visualizzaScadenzaFornitore(idScadenza)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenza").each(
                    function () {

                        $("#datascad_vis").html($(this).find("data").text());
                        $("#notascad_vis").html($(this).find("nota").text());
                        $("#impscad_vis").html($(this).find("importo").text());

                        $("#tipaddeb_vis").html($(this).find("addebito").text());
                        $("#stascad_vis").html($(this).find("stato").text());
                        $("#fatscad_vis").html($(this).find("fattura").text());

                        $("#regOrigTable_vis").html($(this).find("registrazioneOriginante").text());
                        $("#pagamentoTable_vis").html($(this).find("pagamento").text());
                    }
            );
            $("#visualizza-scadenza-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../scadenze/visualizzaScadenzaFornitoreFacade.class.php?modo=start&idScadenza=" + idScadenza, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function modificaScadenzaFornitore(idScadenza)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenza").each(
                    function () {

                        $("#negozio_mod").val($(this).find("negozio").text());
                        $("#datascad_mod").val($(this).find("data").text());
                        $("#notascad_mod").val($(this).find("nota").text());
                        $("#impscad_mod").val($(this).find("importo").text());

                        $("#tipaddeb_mod").html($(this).find("addebito").text());
                        $("#stascad_mod").html($(this).find("stato").text());
                        $("#fatscad_mod").val($(this).find("fattura").text());
                        $("#fatscad_orig_mod").val($(this).find("fattura").text());
                        $("#fornitore_orig_mod").val($(this).find("fornitore").text());

                        $("#regOrigTable_mod").html($(this).find("registrazioneOriginante").text());
                        $("#pagamentoTable_mod").html($(this).find("pagamento").text());
                    }
            );
            $("#modifica-scadenza-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../scadenze/modificaScadenzaFornitoreFacade.class.php?modo=start&idScadenza=" + idScadenza, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

$("#button-ok-modifica-scadenza-form").click(
        function () {
            if (validaModificaScadenza("mod")) {
                $("#testo-messaggio-successo").html("Scadenza fornitore salvata con successo!");
                $("#messaggio-successo-dialog").modal("show");
                sleep(2000);
                $("#modificaScadenzaForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore la scadenza non può essere salvata");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------

$("#button-ok-modifica-scadenza-cliente-form").click(
        function () {
            if (validaModificaScadenza("cli_mod")) {
                $("#testo-messaggio-successo").html("Scadenza cliente salvata con successo!");
                $("#messaggio-successo-dialog").modal("show");
                sleep(2000);
                $("#modificaScadenzaClienteForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore la scadenza non può essere salvata");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

function visualizzaScadenzaCliente(idScadenza)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenza").each(
                    function () {

                        $("#negozio_vis").html($(this).find("negozio").text());
                        $("#datascad_vis").html($(this).find("data").text());
                        $("#notascad_vis").html($(this).find("nota").text());
                        $("#impscad_vis").html($(this).find("importo").text());

                        $("#tipaddeb_vis").html($(this).find("addebito").text());
                        $("#stascad_vis").html($(this).find("stato").text());
                        $("#fatscad_vis").html($(this).find("fattura").text());

                        $("#regOrigTable_vis").html($(this).find("registrazioneOriginante").text());
                        $("#incassoTable_vis").html($(this).find("incasso").text());
                    }
            );
            $("#visualizza-scadenza-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../scadenze/visualizzaScadenzaClienteFacade.class.php?modo=start&idScadenzaCliente=" + idScadenza, true);
    xmlhttp.send();
}

function modificaScadenzaCliente(idScadenza)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState === 4) && (xmlhttp.status === 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("scadenza").each(
                    function () {

                        $("#notascad_cli_mod_control_group").removeClass("has-error");
                        $("#impscad_cli_mod_control_group").removeClass("has-error");
                        $("#fatscad_cli_mod_control_group").removeClass("has-error");
                        $("#datascad_cli_mod_control_group").removeClass("has-error");

                        var negozio = $(this).find("negozio").text();
                        $("#negozio_cli_mod").selectpicker("val", negozio);

                        $("#datascad_cli_mod").val($(this).find("data").text());
                        $("#notascad_cli_mod").val($(this).find("nota").text());
                        $("#impscad_cli_mod").val($(this).find("importo").text());

                        $("#tipaddeb_cli_mod").html($(this).find("addebito").text());
                        $("#stascad_cli_mod").html($(this).find("stato").text());
                        $("#fatscad_cli_mod").val($(this).find("fattura").text());
                        $("#fatscad_orig_cli_mod").val($(this).find("fattura").text());
                        $("#cliente_orig_cli_mod").val($(this).find("cliente").text());

                        $("#regOrigTable_mod").html($(this).find("registrazioneOriginante").text());
                        $("#incassoTable_mod").html($(this).find("incasso").text());
                    }
            );
            $("#modifica-scadenza-cliente-dialog").modal("show");
        }
    };
    xmlhttp.open("GET", "../scadenze/modificaScadenzaClienteFacade.class.php?modo=start&idScadenzaCliente=" + idScadenza, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------
// VALIDAZIONE SCADENZA
//---------------------------------------------------------------------------------

function validaModificaScadenza(type)
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o
     * negativo (0) La validazione complessiva è positiva se tutti i controlli
     * sono positivi (1)
     */
    var esito = "";

    if (isNotEmpty($("#datascad_" + type).val())) {
        if (controllaData("datascad_" + type)) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (isNotEmpty($("#notascad_" + type).val())) {
        if (controllaDescrizione("notascad_" + type)) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (isNotEmpty($("#impscad_" + type).val())) {
        if (controllaImporto("impscad_" + type)) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (isNotEmpty($("#fatscad_" + type).val())) {
        if (controllaNumeroFattura("fatscad_" + type)) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (esito === "1111") {
        return true;
    } else {
        return false;
    }
}


