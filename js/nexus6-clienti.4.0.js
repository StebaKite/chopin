//---------------------------------------------------------------------------------				
// Clienti
//---------------------------------------------------------------------------------

$("#nuovoCliente").click(function () {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
            document.getElementById("nuovoClienteForm").reset();
            if (isNotEmpty(xmlhttp.responseText)) {
                $("#codcli_cre").val(xmlhttp.responseText);
            }
            $("#nuovo-cliente-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "creaClienteFacade.class.php?modo=start", true);
    xmlhttp.send();
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovo-cliente-form").click(
        function () {
            if (validaNuovoCliente()) {
                $("#testo-messaggio-successo").html("Cliente salvato con successo, conto cliente creato!");
                $("#messaggio-successo-dialog").modal("show");
                sleep(2000);
                $("#nuovoClienteForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il cliente non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------

$("#button-ok-modifica-cliente-form").click(
        function () {
            if (validaModificaCliente()) {
                $("#testo-messaggio-successo").html("Cliente salvato con successo");
                $("#messaggio-successo-dialog").modal("show");
                sleep(2000);
                $("#modificaClienteForm").submit();
            } else {
                $("#testo-messaggio-errore").html("In presenza di campi in errore il cliente non può essere salvato");
                $("#messaggio-errore-dialog").modal("show");
            }
        }
);

//---------------------------------------------------------------------------------
// CREA CLIENTE : routine di validazione
//---------------------------------------------------------------------------------

function validaNuovoCliente() {
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o
     * negativo (0) La validazione complessiva è positiva se tutti i controlli
     * sono positivi (1)
     */
    var esito = "";

    if (isNotEmpty($("#codcli_cre").val())) {
        if (controllaCodice("codcli_cre")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (isNotEmpty($("#descli_cre").val())) {
        if (controllaDescrizione("descli_cre")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (esito == "11") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------------------
//MODIFICA CLIENTE : routine di validazione
//---------------------------------------------------------------------------------

function validaModificaCliente() {
    /**
     * Ciascun controllo di validazione può dare un esito positivo (1) o
     * negativo (0) La validazione complessiva è positiva se tutti i controlli
     * sono positivi (1)
     */
    var esito = "";

    if (isNotEmpty($("#codcli_mod").val())) {
        if (controllaCodice("codcli_mod")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (isNotEmpty($("#descli_mod").val())) {
        if (controllaDescrizione("descli_mod")) {
            esito = esito + "1";
        } else {
            esito = esito + "0";
        }
    }

    if (esito == "11") {
        return true;
    } else {
        return false;
    }
}

//---------------------------------------------------------------------------------

function controllaUnivocitaPiva(campo_piva, campo_descli)
{
    var codpiva = $("#" + campo_piva).val();
    var descliente = $("#" + campo_descli).val();

    if (isNotEmpty(codpiva)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    $("#" + campo_piva + "_messaggio").html(xmlhttp.responseText);
                    $("#" + campo_piva + "_control_group").addClass("has-error");
                } else {
                    $("#" + campo_piva + "_messaggio").html("");
                    $("#" + campo_piva + "_control_group").removeClass("has-error");
                }
            }
        }
        xmlhttp.open("GET", "cercaPivaClienteFacade.class.php?modo=start&codpiva=" + codpiva + "&descliente=" + descliente, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

function controllaUnivocitaCfis(campo_cfis)
{
    var codfisc = $("#" + campo_cfis).val();

    if (isNotEmpty(codfisc)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    $("#" + campo_cfis + "_messaggio").html(xmlhttp.responseText);
                    $("#" + campo_cfis + "_control_group").addClass("has-error");
                } else {
                    $("#" + campo_cfis + "_messaggio").html("");
                    $("#" + campo_cfis + "_control_group").removeClass("has-error");
                }
            }
        }
        xmlhttp.open("GET", "cercaCfisClienteFacade.class.php?modo=start&codfisc=" + codfisc, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------

function modificaCliente(idCliente)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            document.getElementById("modificaClienteForm").reset();

            var parser = new DOMParser();
            var xmldoc = parser.parseFromString(xmlhttp.responseText, "application/xml");

            $(xmldoc).find("cliente").each(
                    function () {

                        $("#codcli_mod").val($(this).find("codice").text());
                        $("#descli_mod").val($(this).find("descrizione").text());
                        $("#indcli_mod").val($(this).find("indirizzo").text());
                        $("#cittacli_mod").val($(this).find("citta").text());
                        $("#capcli_mod").val($(this).find("cap").text());
                        $("#pivacli_mod").val($(this).find("piva").text());
                        $("#cfiscli_mod").val($(this).find("cfisc").text());

                        $("#bonifico_mod").parent('.btn').removeClass('active');
                        $("#riba_mod").parent('.btn').removeClass('active');
                        $("#rimdiretta_mod").parent('.btn').removeClass('active');
                        $("#assegnobancario_mod").parent('.btn').removeClass('active');
                        $("#addebitodiretto_mod").parent('.btn').removeClass('active');

                        var tipoAddebito = $(this).find("tipoAddebito").text();

                        if (tipoAddebito == "BONIFICO") {
                            $("#bonifico_mod").parent('.btn').addClass('active');
                            $("#bonifico_mod").prop('checked', true);
                        }
                        if (tipoAddebito == "RIBA") {
                            $("#riba_mod").parent('.btn').addClass('active');
                            $("#riba_mod").prop('checked', true);
                        }
                        if (tipoAddebito == "RIM_DIR") {
                            $("#rimdiretta_mod").parent('.btn').addClass('active');
                            $("#rimdiretta_mod").prop('checked', true);
                        }
                        if (tipoAddebito == "ASS_BAN") {
                            $("#assegnobancario_mod").parent('.btn').addClass('active');
                            $("#assegnobancario_mod").prop('checked', true);
                        }
                        if (tipoAddebito == "ADD_DIR") {
                            $("#addebitodiretto_mod").parent('.btn').addClass('active');
                            $("#addebitodiretto_mod").prop('checked', true);
                        }

                        $("#categoriacli_mod").html($(this).find("categorieCliente").text());
                        var categoria = $(this).find("categoria").text();
                        $("#categoriacli_mod").selectpicker('val', categoria);
                        $("#catcli_mod").val(categoria);
                    }
            )

            $("#modifica-cliente-dialog").modal("show");
        }
    }
    xmlhttp.open("GET", "modificaClienteFacade.class.php?modo=start&idcliente=" + idCliente, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function cancellaCliente(idcliente) {
    $("#idcliente").val(idcliente);
    $("#cancella-cliente-dialog").modal("show");
}

//---------------------------------------------------------------------------------

$("#button-ok-cancella-cliente-form").click(
        function () {
            $("#testo-messaggio-successo").html("Cliente cancellato!");
            $("#messaggio-successo-dialog").modal("show");
            sleep(2000);
            $("#cancellaClienteForm").submit();
        }
);

