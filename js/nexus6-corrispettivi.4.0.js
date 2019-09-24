//---------------------------------------------------------------------------------
// Corrispettivi
//---------------------------------------------------------------------------------

$("#nuovo-corrispettivo-mercato").click(function (event) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
            document.getElementById("nuovoCorrispettivoMercatoForm").reset();
            $("#codneg_cormer_cre").selectpicker('val', ' ');
            $("#causale_cormer_cre").selectpicker('val', ' ');
            $("#dettagli_cormer_cre").html("");
            $("#dettagli_cormer_cre_messaggio").html("");
            $("#nuovo-corrispettivo-mercato-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "creaCorrispettivoMercatoFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#nuovo-corrispettivo-negozio").click(function (event) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
            document.getElementById("nuovoCorrispettivoNegozioForm").reset();
            $("#codneg_corneg_cre").selectpicker('val', ' ');
            $("#causale_corneg_cre").selectpicker('val', ' ');
            $("#dettagli_corneg_cre").html("");
            $("#dettagli_corneg_cre_messaggio").html("");
            $("#nuovo-corrispettivo-negozio-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "creaCorrispettivoNegozioFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#codneg_cormer_cre").change(
        function () {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    $("#mercato_cormer_cre").html(xmlhttp.responseText);
                    $("#mercato_cormer_cre").selectpicker("refresh");
                    loadContiCorrispettivoMercato();
                }
            }
            xmlhttp.open("GET", "leggiMercatiNegozioFacade.class.php?modo=start&codneg_cormer_cre=" + this.value, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#codneg_cormer_mod").change(
        function () {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    $("#mercato_cormer_mod").html(xmlhttp.responseText);
                    $("#mercato_cormer_mod").selectpicker("refresh");
                    loadContiCorrispettivoMercato();
                }
            }
            xmlhttp.open("GET", "leggiMercatiNegozioFacade.class.php?modo=start&codneg_cormer_mod=" + this.value, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuovo-corrispettivo-mercato-form").click(function () {
    $("#nuovo-dettaglio-corrispettivo-mercato-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-corrispettivo-mercato-form").click(function () {
    $("#nuovo-dettaglio-modifica-corrispettivo-mercato-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-nuovo-corrispettivo-negozio-form").click(function () {
    $("#nuovo-dettaglio-corrispettivo-negozio-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-nuovo-dettaglio-modifica-corrispettivo-negozio-form").click(function () {
    $("#nuovo-dettaglio-modifica-corrispettivo-negozio-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuovo-corrispettivo-mercato-form").click(
        function () {

            var importo = $("#importo_cormer_cre").val();
            var importoNormalizzato = importo.trim().replace(",", ".");
            var imponibile = $("#imponibile_cormer_cre").val();
            var imponibileNormalizzato = imponibile.trim().replace(",", ".");
            var iva = $("#iva_cormer_cre").val();
            var ivaNormalizzato = iva.trim().replace(",", ".");

            if ($('#aliquota10_cormer_cre').is(':checked')) {
                var aliquota = $("#aliquota10_cormer_cre").val();
            }
            if ($('#aliquota20_cormer_cre').is(':checked')) {
                var aliquota = $("#aliquota20_cormer_cre").val();
            }

            var conto = $("#conti_cormer_cre").val().replace(",", ".");			// tolgo eventuali virgole nella descrizione del conto
            var idconto = conto.substring(0, 6);

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#dettagli_cormer_cre").html(dettagliTable);
                    $("#dettagli_cormer_cre").show();
                    controllaDettagliRegistrazione("dettagli_cormer_cre");
                }
            }
            xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoMercatoFacade.class.php?modo=go&codconto_cormer_cre=" + idconto + "&aliquota_cormer_cre=" + aliquota + "&importo_cormer_cre=" + importoNormalizzato + "&iva_cormer_cre=" + ivaNormalizzato + "&imponibile_cormer_cre=" + imponibileNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuovo-corrispettivo-negozio-form").click(
        function () {

            var importo = $("#importo_corneg_cre").val();
            var importoNormalizzato = importo.trim().replace(",", ".");
            var imponibile = $("#imponibile_corneg_cre").val();
            var imponibileNormalizzato = imponibile.trim().replace(",", ".");
            var iva = $("#iva_corneg_cre").val();
            var ivaNormalizzato = iva.trim().replace(",", ".");

            if ($('#aliquota10_corneg_cre').is(':checked')) {
                var aliquota = $("#aliquota10_corneg_cre").val();
            }
            if ($('#aliquota20_corneg_cre').is(':checked')) {
                var aliquota = $("#aliquota20_corneg_cre").val();
            }

            var conto = $("#conti_corneg_cre").val().replace(",", ".");			// tolgo eventuali virgole nella descrizione del conto
            var idconto = conto.substring(0, 6);

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#dettagli_corneg_cre").html(dettagliTable);
                    $("#dettagli_corneg_cre").show();
                    controllaDettagliRegistrazione("dettagli_corneg_cre");
                }
            }
            xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoNegozioFacade.class.php?modo=go&codconto_corneg_cre=" + idconto + "&aliquota_corneg_cre=" + aliquota + "&importo_corneg_cre=" + importoNormalizzato + "&iva_corneg_cre=" + ivaNormalizzato + "&imponibile_corneg_cre=" + imponibileNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-corrispettivo-mercato-form").click(
        function () {

            var importo = $("#importo_cormer_mod").val();
            var importoNormalizzato = importo.trim().replace(",", ".");
            var imponibile = $("#imponibile_cormer_mod").val();
            var imponibileNormalizzato = imponibile.trim().replace(",", ".");
            var iva = $("#iva_cormer_mod").val();
            var ivaNormalizzato = iva.trim().replace(",", ".");

            if ($('#aliquota10_cormer_mod').is(':checked')) {
                var aliquota = $("#aliquota10_cormer_mod").val();
            }
            if ($('#aliquota20_cormer_mod').is(':checked')) {
                var aliquota = $("#aliquota20_cormer_mod").val();
            }

            var conto = $("#conti_cormer_mod").val().replace(",", ".");			// tolgo eventuali virgole nella descrizione del conto
            var idconto = conto.substring(0, 6);

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#dettagli_cormer_mod").html(dettagliTable);
                    $("#dettagli_cormer_mod").show();
                    controllaDettagliRegistrazione("dettagli_cormer_mod");
                }
            }
            xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoMercatoFacade.class.php?modo=go&codconto_cormer_mod=" + idconto + "&aliquota_cormer_mod=" + aliquota + "&importo_cormer_mod=" + importoNormalizzato + "&iva_cormer_mod=" + ivaNormalizzato + "&imponibile_cormer_mod=" + imponibileNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-modifica-corrispettivo-negozio-form").click(
        function () {

            var importo = $("#importo_corneg_mod").val();
            var importoNormalizzato = importo.trim().replace(",", ".");
            var imponibile = $("#imponibile_corneg_mod").val();
            var imponibileNormalizzato = imponibile.trim().replace(",", ".");
            var iva = $("#iva_corneg_mod").val();
            var ivaNormalizzato = iva.trim().replace(",", ".");
            var aliquota = "";

            if ($('#aliquota10_corneg_mod').is(':checked')) {
                aliquota = $("#aliquota10_corneg_mod").val();
            }
            if ($('#aliquota20_corneg_mod').is(':checked')) {
                aliquota = $("#aliquota20_corneg_mod").val();
            }

            var conto = $("#conti_corneg_mod").val().replace(",", ".");			// tolgo eventuali virgole nella descrizione del conto
            var idconto = conto.substring(0, 6);

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#dettagli_corneg_mod").html(dettagliTable);
                    $("#dettagli_corneg_mod").show();
                    controllaDettagliRegistrazione("dettagli_corneg_mod");
                }
            }
            xmlhttp.open("GET", "aggiungiNuovoDettaglioCorrispettivoNegozioFacade.class.php?modo=go&codconto_corneg_mod=" + idconto + "&aliquota_corneg_mod=" + aliquota + "&importo_corneg_mod=" + importoNormalizzato + "&iva_corneg_mod=" + ivaNormalizzato + "&imponibile_corneg_mod=" + imponibileNormalizzato, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

function loadContiCorrispettivoMercato() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange =
            function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    $("#conti_cormer_cre").html(xmlhttp.responseText);
                    $("#conti_cormer_cre").selectpicker("refresh");
                }
            }
    xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=2105", true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#codneg_corneg_cre").change(
    function () {
        if (isNotEmpty(causale)) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange =
                    function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            $("#conti_corneg_cre").html(xmlhttp.responseText);
                            $("#conti_corneg_cre").selectpicker("refresh");
                        }
                    }
            xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=2100", true);
            xmlhttp.send();
        }
    }
);

//---------------------------------------------------------------------------------

$("#codneg_corneg_mod").change(
    function () {
        var causale = $("#causale_corneg_mod").val();

        if (isNotEmpty(causale)) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange =
                    function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            $("#conti_corneg_mod").html(xmlhttp.responseText);
                            $("#conti_corneg_mod").selectpicker("refresh");
                        }
                    }
            xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=2100", true);
            xmlhttp.send();
        }
    }
);

//---------------------------------------------------------------------------------

function calcolaImponibile(campoImporto, campoAliquota, campoImponibile, campoIva, msgSquadratura)
{
    var importo = $("#" + campoImporto).val();
    var importoNormalizzato = importo.trim().replace(",", ".");

    var aliquota = $("input[name=" + campoAliquota + "]:checked").val();

    var imponibile = importoNormalizzato / aliquota;
    var imponibileArrotondato = imponibile.toFixed(2);

    $("#" + campoImponibile).val(imponibileArrotondato);

    if (aliquota == "1.10") {
        var iva = imponibileArrotondato * 0.1;
    } else {
        var iva = imponibileArrotondato * 0.22;
    }

    var ivaArrotondata = iva.toFixed(2);
    $("#" + campoIva).val(ivaArrotondata);

    // Breve controllo di quadratura degli importi

    var sommaImportiCalcolati = parseFloat(imponibileArrotondato) + parseFloat(ivaArrotondata);
    var sommaImportiCalcolatiArrotondato = sommaImportiCalcolati.toFixed(2);

    var importoSquadratura = importoNormalizzato - parseFloat(sommaImportiCalcolatiArrotondato);

    if (importoSquadratura != 0) {
        $("#" + msgSquadratura).html("ATTENZIONE, squadratura di &euro; " + importoSquadratura.toFixed(2) + " Correggi manualmente");
    } else {
        $("#" + msgSquadratura).html("");
    }
}

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-corrispettivo-mercato-form").click(
        function () {
            if (validaNuovoCorrispettivoMercato()) {
                $("#testo-messaggio-successo").html("Corrispettivo mercato salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("nuovo-corrispettivo-mercato-dialog").modal("hide");
                $("#nuovoCorrispettivoMercatoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il corrispettivo non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-corrispettivo-negozio-form").click(
        function () {
            if (validaNuovoCorrispettivoNegozio()) {
                $("#testo-messaggio-successo").html("Corrispettivo negozio salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("nuovo-corrispettivo-negozio-dialog").modal("hide");
                $("#nuovoCorrispettivoNegozioForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il corrispettivo non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------
//CREA NUOVO CORRISPETTIVO MERCATO : controllo campi in pagina
//---------------------------------------------------------------------------------

function validaNuovoCorrispettivoMercato()
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
     * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
     */
    var esito = "";

    controllaDataRegistrazione("datareg_cormer_cre");
    if (isEmpty($("#datareg_cormer_cre_messaggio").text()))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (isNotEmpty($("#descreg_cormer_cre").val())) {
        if (controllaDescrizione("descreg_cormer_cre"))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    controllaDettagliRegistrazione("dettagli_cormer_cre");
    if (isEmpty($("#dettagli_cormer_cre_messaggio").text()))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (esito == "111") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------------------
//CREA NUOVO CORRISPETTIVO NEGOZIO : controllo campi in pagina
//---------------------------------------------------------------------------------

function validaNuovoCorrispettivoNegozio()
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
     * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
     */
    var esito = "";

    controllaDataRegistrazione("datareg_corneg_cre");
    if (isEmpty($("#datareg_corneg_cre_messaggio").text()))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (isNotEmpty($("#descreg_corneg_cre").val())) {
        if (controllaDescrizione("descreg_corneg_cre"))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    controllaDettagliRegistrazione("dettagli_corneg_cre");
    if (isEmpty($("#dettagli_corneg_cre_messaggio").text()))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (esito == "111") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------

function visualizzaCorrispettivoMercato(idRegistrazione)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("corrispettivo").each(
                    function () {

                        $("#datareg_cormer_vis").html($(this).find("datareg").text());
                        $("#descreg_cormer_vis").html($(this).find("descreg").text());
                        $("#causale_cormer_vis").html($(this).find("causale").text());
                        $("#codneg_cormer_vis").html($(this).find("codneg").text());

                        var mercato = $(this).find("mercato").text();

                        $("#mercato_cormer_vis").html(mercato);
                        $("#dettagli_cormer_vis").html($(this).find("dettagli").text());
                    }
            )

            $("#visualizza-corrispettivo-mercato-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "visualizzaCorrispettivoMercatoFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function visualizzaCorrispettivoNegozio(idRegistrazione)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("corrispettivo").each(
                    function () {

                        $("#datareg_corneg_vis").html($(this).find("datareg").text());
                        $("#descreg_corneg_vis").html($(this).find("descreg").text());
                        $("#causale_corneg_vis").html($(this).find("causale").text());
                        $("#codneg_corneg_vis").html($(this).find("codneg").text());
                        $("#dettagli_corneg_vis").html($(this).find("dettagli").text());
                    }
            )

            $("#visualizza-corrispettivo-negozio-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "visualizzaCorrispettivoNegozioFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------
//MODIFICA CORRISPETTIVO MERCATO : controllo campi in pagina
//---------------------------------------------------------------------------------

function validaModificaCorrispettivoMercato()
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
     * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
     */
    var esito = "";

    controllaDataRegistrazione("datareg_cormer_mod");
    if (isEmpty($("#datareg_cormer_mod_messaggio").text()))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (isNotEmpty($("#descreg_cormer_mod").val())) {
        if (controllaDescrizione("descreg_cormer_mod"))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if (isNotEmpty($("#causale_cormer_mod").val())) {
        controllaDettagliRegistrazione("dettagli_cormer_mod");
        if (isEmpty($("#dettagli_cormer_mod_messaggio").text()))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if (esito == "111") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------

function modificaCorrispettivoMercato(idRegistrazione)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("corrispettivo").each(
                    function () {

                        $("#datareg_cormer_mod").val($(this).find("datareg").text());
                        $("#descreg_cormer_mod").val($(this).find("descreg").text());

                        var causale = $(this).find("causale").text();
                        $("#causale_cormer_mod").selectpicker('val', causale);

                        var negozio = $(this).find("codneg").text();
                        $("#codneg_cormer_mod").selectpicker('val', negozio);

                        $("#mercato_cormer_mod").html($(this).find("mercatiNegozio").text());
                        $("#mercato_cormer_mod").selectpicker('refresh');

                        var mercato = $(this).find("mercato").text();
                        $("#mercato_cormer_mod").selectpicker('val', mercato);

                        $("#dettagli_cormer_mod").html($(this).find("dettagli").text());
                        $("#conti_cormer_mod").html($(this).find("contiCausale").text());
                    }
            )

            $("#modifica-corrispettivo-mercato-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "modificaCorrispettivoMercatoFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#button-ok-modifica-corrispettivo-mercato-form").click(
        function () {
            if (validaModificaCorrispettivoMercato()) {
                $("#testo-messaggio-successo").html("Corrispettivo salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("modifica-corrispettivo-mercato-dialog").modal("hide");
                $("#modificaCorrispettivoMercatoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il corrispettivo non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------
//MODIFICA CORRISPETTIVO NEGOZIO : controllo campi in pagina
//---------------------------------------------------------------------------------

function validaModificaCorrispettivoNegozio()
{
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o negativo (0)
     * La validazione complessiva è positiva se tutti i controlli sono positivi (1)
     */
    var esito = "";

    controllaDataRegistrazione("datareg_corneg_mod");
    if (isEmpty($("#datareg_corneg_mod_messaggio").text()))
        esito = esito + "1";
    else
        esito = esito + "0";

    if (isNotEmpty($("#descreg_corneg_mod").val())) {
        if (controllaDescrizione("descreg_corneg_mod"))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if (isNotEmpty($("#causale_corneg_mod").val())) {
        controllaDettagliRegistrazione("dettagli_corneg_mod");
        if (isEmpty($("#dettagli_corneg_mod_messaggio").text()))
            esito = esito + "1";
        else
            esito = esito + "0";
    }

    if (esito == "111") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------

function modificaCorrispettivoNegozio(idRegistrazione)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("corrispettivo").each(
                    function () {

                        $("#datareg_corneg_mod").val($(this).find("datareg").text());
                        $("#descreg_corneg_mod").val($(this).find("descreg").text());

                        var causale = $(this).find("causale").text();
                        $("#causale_corneg_mod").selectpicker('val', causale);

                        var negozio = $(this).find("codneg").text();
                        $("#codneg_corneg_mod").selectpicker('val', negozio);

                        $("#dettagli_corneg_mod").html($(this).find("dettagli").text());
                        
                        var conti = $(this).find("contiCausale").text();
                        $("#conti_corneg_mod").selectpicker('val', conti);
                        $("#conti_corneg_mod").selectpicker('refresh');
                    }
            )

            $("#modifica-corrispettivo-negozio-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "modificaCorrispettivoNegozioFacade.class.php?modo=start&idreg=" + idRegistrazione, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#button-ok-modifica-corrispettivo-negozio-form").click(
        function () {
            if (validaModificaCorrispettivoNegozio()) {
                $("#testo-messaggio-successo").html("Corrispettivo salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                $("modifica-corrispettivo-negozio-dialog").modal("hide");
                $("#modificaCorrispettivoNegozioForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il corrispettivo non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

function modificaDettaglioCorrispettivo(idTable, conto, sottoconto, importo, segno, idDettaglio)
{
    var importoDettNormalizzato;
    if (isEmpty(importo))
        importoDettNormalizzato = 0;
    else
        importoDettNormalizzato = importo.trim().replace(",", ".");

    if (isNotEmpty(conto)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    var dettagliTable = xmlhttp.responseText;
                    $("#" + idTable).html(dettagliTable);

                    controllaDettagliRegistrazione(idTable);
                }
            }
        };
        xmlhttp.open("GET", "../primanota/aggiornaDettaglioCorrispettivoFacade.class.php?modo=go&codconto=" + conto + "&codsottoconto=" + sottoconto + "&importo=" + importoDettNormalizzato + "&dareAvere=" + segno + "&iddettaglio=" + idDettaglio, true);
        xmlhttp.send();
    }   
}

//---------------------------------------------------------------------------------

function cancellaDettaglioNuovoCorrispettivo(idTable, codContoComposto)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            var dettagliTable = xmlhttp.responseText;
            $("#" + idTable).html(dettagliTable);
            controllaDettagliRegistrazione(idTable);
        }
    };
    xmlhttp.open("GET", "../primanota/cancellaNuovoDettaglioCorrispettivoFacade.class.php?modo=go&codconto=" + codContoComposto, true);
    xmlhttp.send();
}

