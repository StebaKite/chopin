//---------------------------------------------------------------------------------
// Fatture
//---------------------------------------------------------------------------------

$("#nuovo-dett-fattura-aziende").click(function () {
    $("#nuovo-dettaglio-fattura-aziende-dialog").modal("show");
});

$("#nuovo-dett-fattura-cliente").click(function () {
    $("#nuovo-dettaglio-fattura-cliente-dialog").modal("show");
});

//---------------------------------------------------------------------------------

$("#button-ok-nuovodett-nuova-fattura").click(
        function () {

            var quantita = $("#quantita").val();
            var articolo = $("#articolo").val();
            var importo = $("#importo").val();
            var totale = $("#totale").val();
            var imponibile = $("#imponibile").val();
            var iva = $("#iva").val();

            var aliquota = $("input[name=aliquota]:checked").val();

            if (aliquota == "1.05") {
                var aliq = 5;
            }
            if (aliquota == "1.10") {
                var aliq = 10;
            }
            if (aliquota == "1.22") {
                var aliq = 22;
            }
            if (aliquota == "1") {
                var aliq = "";
            }

            // normalizzo la virgola dell'importo
            var importoNormalizzato = importo.trim().replace(",", ".");

            // normalizzo la descrizione dell'articolo
            var articoloNormalizzato = articolo.trim().replace("&", " ");

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange =
                    function () {
                        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                            var dettagliTable = xmlhttp.responseText;
                            $("#dettagli").html(dettagliTable);
                        }
                    };
            xmlhttp.open("GET", "aggiungiNuovoDettaglioFatturaFacade.class.php?modo=go&quantita=" + quantita + "&articolo=" + articoloNormalizzato + "&importo=" + importoNormalizzato + "&aliquota=" + aliq + "&totale=" + totale + "&imponibile=" + imponibile + "&iva=" + iva, true);
            xmlhttp.send();
        }
);

//---------------------------------------------------------------------------------

function calcolaImponibileFattura() {

    var importo = $("#importo").val();
    var quantita = $("#quantita").val();

    var importoNormalizzato = importo.trim().replace(",", ".");

    if (importoNormalizzato > 0) {

        var totale = importoNormalizzato * quantita;
        $("#totale").val(totale.toFixed(2));

        imponibileFattura();
    }
}

//---------------------------------------------------------------------------------

function imponibileFattura() {

    var totale = $("#totale").val();
    var totaleNormalizzato = totale.trim().replace(",", ".");

    var aliquota = $("input[name=aliquota]:checked").val();

    var imponibile = totaleNormalizzato / aliquota;
    var imponibileArrotondato = imponibile.toFixed(2);

    $("#imponibile").val(imponibileArrotondato);

    if (aliquota === "1.05") {
        var iva = imponibileArrotondato * 0.05;
    }
    if (aliquota == "1.10") {
        var iva = imponibileArrotondato * 0.10;
    }
    if (aliquota == "1.22") {
        var iva = imponibileArrotondato * 0.22;
    }
    if (aliquota == "1") {
        var iva = 0;
    }

    var ivaArrotondata = iva.toFixed(2);
    $("#iva").val(ivaArrotondata);
}

//---------------------------------------------------------------------------------

function prelevaNumeroFattura(negozio, catcliente) {

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            $("#numfat").val(xmlhttp.responseText);
        }
    }
    xmlhttp.open("GET", "prelevaProgressivoFatturaFacade.class.php?modo=start&catcliente=" + catcliente + "&codneg=" + negozio, true);
    xmlhttp.send();

    // Inizializzazione della banca. Uguale per tutti i negozi

    $("#ragsocbanca").val("BCC Treviglio - Filiale di Carvico");
    $("#ibanbanca").val("IT48F0889952780000000420186");
}

//---------------------------------------------------------------------------------

function prelevaTipoAddebitoCliente(idCliente) {

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            $("#tipoadd").val(xmlhttp.responseText);
        }
    }
    xmlhttp.open("GET", "prelevaTipoAddebitoClienteFacade.class.php?modo=start&idcliente=" + idCliente, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

$("#crea-fattura-cliente").click(function (event) {
    $("#nuovaFatturaForm").submit();
});

//---------------------------------------------------------------------------------

function gestisciAssistito(tipofattura) {

    if (tipofattura == "CONTRIBUTO") {
        $("#linea_assistito").show();
    } else {
        $("#linea_assistito").hide();
    }
}

//---------------------------------------------------------------------------------

function cancellaDettaglioFattura(idarticolo) {

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            var dettagliTable = xmlhttp.responseText;
            $("#dettagli").html(dettagliTable);
        }
    };
    xmlhttp.open("GET", "cancellaNuovoDettaglioFatturaFacade.class.php?modo=go&idarticolo=" + idarticolo, true);
    xmlhttp.send();
}