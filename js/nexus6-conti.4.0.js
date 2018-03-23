//---------------------------------------------------------------------------------
// Conti e Sottoconti
//---------------------------------------------------------------------------------

$("#nuovoConto").click(function () {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
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

$("#button-nuovo-sottoconto-nuovo-conto-form").click(function () {

    var codconto = $("#codconto_cre").val();
    var desconto = $("#desconto_cre").val();
    var numrigabilancio = $("#numrigabilancio_cre").val();

    $("#desconto_cre_control_group").removeClass("has-error");

    if (codconto == "")
        $("#codconto_cre_control_group").addClass("has-error");
    if (desconto == "")
        $("#desconto_cre_control_group").addClass("has-error");
    if (!controllaNumero("numrigabilancio_cre"))
        $("#numrigabilancio_cre_control_group").addClass("has-error");

    if ((codconto != "") && (desconto != "") && (numrigabilancio != "")) {
        if (!$("#codconto_cre_control_group").hasClass("has-error") && !$("#numrigabilancio_cre_control_group").hasClass("has-error"))
            $("#nuovo-sottoconto-nuovo-conto-dialog").modal("show");
    }
});

//---------------------------------------------------------------------------------

$("#button-nuovo-sottoconto-modifica-conto-form").click(function () {

    var codconto = $("#codconto_mod").val();
    var desconto = $("#desconto_mod").val();
    var numrigabilancio = $("#numrigabilancio_mod").val();

    $("#desconto_mod_control_group").removeClass("has-error");

    if (codconto == "")
        $("#codconto_mod_control_group").addClass("has-error");
    if (desconto == "")
        $("#desconto_mod_control_group").addClass("has-error");
    if (!controllaNumero("numrigabilancio_mod"))
        $("#numrigabilancio_mod_control_group").addClass("has-error");

    if ((codconto != "") && (desconto != "") && (numrigabilancio != "")) {
        if (!$("#codconto_mod_control_group").hasClass("has-error") && !$("#numrigabilancio_mod_control_group").hasClass("has-error"))
            $("#nuovo-sottoconto-modifica-conto-dialog").modal("show");
    }
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-sottoconto-nuovo-conto-form").click(function () {

    var codconto = $("#codconto_cre").val();
    var codsottoconto = $("#codsottoconto_cre").val();
    var dessottoconto = $("#dessottoconto_cre").val();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            $("#sottocontiTable_cre").html(xmlhttp.responseText);
        }
    }
    xmlhttp.open("GET", "aggiungiNuovoSottocontoFacade.class.php?modo=start&codsottoconto=" + codsottoconto + "&dessottoconto=" + dessottoconto + "&codconto=" + codconto, true);
    xmlhttp.send();
}
);

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-sottoconto-modifica-conto-form").click(function () {

    var codconto = $("#codconto_mod").val();
    var codsottoconto = $("#codsottoconto_mod").val();
    var dessottoconto = $("#dessottoconto_mod").val();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
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
        function () {
            if (validaNuovoConto()) {
                $("#testo-messaggio-successo").html("Conto salvato con successo!");
                $("#messaggio-successo-dialog").modal("show");
                sleep(3000);
                $("#nuovoContoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il conto non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-conto-form").click(
        function () {
            if (validaModificaConto()) {
                $("#testo-messaggio-successo").html("Conto salvato con successo");
                $("#messaggio-successo-dialog").modal("show");
                sleep(3000);
                $("#modificaContoForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il conto non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-gruppo-sottoconto-form").click(function () {

    var codconto = $("#codconto_modgru").val();
    var codsottoconto = $("#codsottoconto_modgru").val();
    if ($("#indgruppoNS_modgru").parent(".btn").hasClass("active"))
        var indgruppo = $("#indgruppoNS_modgru").val();
    if ($("#indgruppoCF_modgru").parent(".btn").hasClass("active"))
        var indgruppo = $("#indgruppoCF_modgru").val();
    if ($("#indgruppoCV_modgru").parent(".btn").hasClass("active"))
        var indgruppo = $("#indgruppoCV_modgru").val();
    if ($("#indgruppoRC_modgru").parent(".btn").hasClass("active"))
        var indgruppo = $("#indgruppoRC_modgru").val();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
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
    xmlhttp.onreadystatechange = function () {
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
        function () {
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
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("conto").each(
                    function () {

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
                            $("#contoeco_mod").prop('checked', true);
                        }
                        if (categoria == "Stato Patrimoniale") {
                            $("#contopat_mod").parent('.btn').addClass('active');
                            $("#contopat_mod").prop('checked', true);
                        }
                        //----------------------------------------------------------
                        if (tipo == "Dare") {
                            $("#dare_mod").parent('.btn').addClass('active');
                            $("#dare_mod").prop('checked', true);
                        }
                        if (tipo == "Avere") {
                            $("#avere_mod").parent('.btn').addClass('active');
                            $("#avere_mod").prop('checked', true);
                        }
                        //----------------------------------------------------------
                        if (presenzaInBil == "S") {
                            $("#presenzaSi_mod").parent('.btn').addClass('active');
                            $("#presenzaSi_mod").prop('checked', true);
                        }
                        if (presenzaInBil == "N") {
                            $("#presenzaNo_mod").parent('.btn').addClass('active');
                            $("#presenzaNo_mod").prop('checked', true);
                        }
                        //----------------------------------------------------------
                        if (presenzaSottoconti == "S") {
                            $("#sottocontiSi_mod").parent('.btn').addClass('active');
                            $("#sottocontiSi_mod").prop('checked', true);
                        }
                        if (presenzaSottoconti == "N") {
                            $("#sottocontiNo_mod").parent('.btn').addClass('active');
                            $("#sottocontiNo_mod").prop('checked', true);
                        }

                        //----------------------------------------------------------

                        $("#sottocontiTable_mod").html($(this).find("sottoconti").text());
                    }
            )

            $("#modifica-conto-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "modificaContoFacade.class.php?modo=start&codconto=" + codConto, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function visualizzaConto(codConto)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("conto").each(
                    function () {

                        $("#codconto_vis").html($(this).find("codice").text());
                        $("#desconto_vis").html($(this).find("descrizione").text());
                        $("#numrigabilancio_vis").html($(this).find("numeroRigaBilancio").text());

                        $("#catconto_vis").html($(this).find("categoria").text());
                        $("#tipconto_vis").html($(this).find("tipo").text());

                        var presInBil = $(this).find("presenzaInBilancio").text();
                        if (presInBil == "N")
                            $("#indpresenza_vis").html("Escluso");
                        else
                            $("#indpresenza_vis").html("Incluso in Bilancio");

                        var presSottoconti = $(this).find("presenzaSottoconti").text();
                        if (presSottoconti == "N")
                            $("#indvissottoconti_vis").html("Non visibili");
                        else
                            $("#indvissottoconti_vis").html("Sottoconti visibili");

                        $("#datareg_da").val(getTodayDate());
                        $("#datareg_a").val(getTodayDate());


                        //----------------------------------------------------------

                        $("#sottocontiTable_vis").html($(this).find("sottoconti").text());
                    }
            )
            $("#visualizza-conto-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "visualizzaContoFacade.class.php?modo=start&codconto=" + codConto, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------

function estraiMovimentiSottoconto(codconto, codsottoconto)
{
    var negozio = $("#codneg_sel").val();
    var datareg_DA = $("#datareg_da").val();
    var datareg_A = $("#datareg_a").val();
    var saldiSi = $("#saldiInclusiSi").val();
    var saldiNo = $("#saldiInclusiNo").val();
    if ($("#saldiInclusiSi").parent(".btn").hasClass("active"))
        var saldi = "S";
    if ($("#saldiInclusiNo").parent(".btn").hasClass("active"))
        var saldi = "N";

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("sottoconto").each(
                    function () {

                        $("#movimentiTable_vis").html($(this).find("movimenti").text());
                    }
            )
            $("#visualizza-movimenti-sottoconto-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "estraiMovimentiSottocontoFacade.class.php?modo=start&csot_mov=" + codsottoconto + "&ccon_mov=" + codconto + "&cneg_mov=" + negozio + "&dtda_mov=" + datareg_DA + "&dta_mov=" + datareg_A + "&sal_mov=" + saldi, true);
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
        if (controllaCodice("codconto_cre")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if ($("#desconto_cre").val() != "") {
        if (controllaDescrizione("desconto_cre")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if ($("#numrigabilancio_cre").val() != "") {
        if (controllaNumero("numrigabilancio_cre")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (esito == "111") {
        return true;
    } else {
        return false;
    }
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
        if (controllaCodice("codconto_mod")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if ($("#desconto_mod").val() != "") {
        if (controllaDescrizione("desconto_mod")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if ($("#numrigabilancio_mod").val() != "") {
        if (controllaNumero("numrigabilancio_mod")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (esito == "111") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------

function controllaConto(campoCodConto)
{
    $("#" + campoCodConto + "_control_group").removeClass("has-error");

    codConto = $("#" + campoCodConto).val();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
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

function modificaGruppoSottoconto(indgruppo, codconto, codsottoconto)
{
    $("#codconto_modgru").val(codconto);
    $("#codsottoconto_modgru").val(codsottoconto);

    $("#indgruppoNS_modgru").parent('.btn').removeClass('active');
    $("#indgruppoCF_modgru").parent('.btn').removeClass('active');
    $("#indgruppoCV_modgru").parent('.btn').removeClass('active');
    $("#indgruppoRC_modgru").parent('.btn').removeClass('active');

    if (indgruppo == "NS") {
        $("#indgruppoNS_modgru").parent('.btn').addClass('active');
        $("#indgruppoNS_modgru").prop('checked', true);
    }
    if (indgruppo == "CF") {
        $("#indgruppoCF_modgru").parent('.btn').addClass('active');
        $("#indgruppoCF_modgru").prop('checked', true);
    }
    if (indgruppo == "CV") {
        $("#indgruppoCV_modgru").parent('.btn').addClass('active');
        $("#indgruppoCV_modgru").prop('checked', true);
    }
    if (indgruppo == "RC") {
        $("#indgruppoRC_modgru").parent('.btn').addClass('active');
        $("#indgruppoRC_modgru").prop('checked', true);
    }

    $("#modifica-gruppo-sottoconto-dialog").modal("show");
}

//---------------------------------------------------------------------
