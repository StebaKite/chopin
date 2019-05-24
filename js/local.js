$('select').selectpicker();

$('.selectNormal').selectpicker({
    style: 'btn-info',
    size: 'auto'
});

$('.selectCausale').selectpicker({
    style: 'btn-info',
    size: 'auto',
    width: '300px'
});

$('.selectNegozio').selectpicker({
    style: 'btn-info',
    size: 'auto'
});

$('.selectCliFor').selectpicker({
    style: 'btn-info',
    size: 'auto'
});

$('.selectCatCli').selectpicker({
    style: 'btn-info',
    size: 'auto'
});

// -----------------------------------------------------------------
// Ajax su campi di input
// -----------------------------------------------------------------

$("#messaggioInfo").animate({opacity: 1.0}, 5000).effect("fade", 3500).fadeOut('slow');
$("#messaggioErrore").animate({opacity: 1.0}, 5000).effect("fade", 6000).fadeOut('slow');


// Hover states on the static widgets
$("#dialog-link, #icons li").hover(
        function () {
            $(this).addClass("ui-state-hover");
        },
        function () {
            $(this).removeClass("ui-state-hover");
        }
);
  
$('.modal-dialog').draggable();

//---------------------------------------------------------------
// Funzioni comuni
//---------------------------------------------------------------

function pad(num, size)
{
    var s = num + "";
    while (s.length < size)
        s = "0" + s;
    return s;
}

function isNumeric(val)
{
    var pattern = /^[-+]?(\d+|\d+\.\d*|\d*\.\d+)$/;
    return pattern.test(val);
}

function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

function sleep(miliseconds)
{
    var currentTime = new Date().getTime();
    while (currentTime + miliseconds >= new Date().getTime()) {
    }
}

function getTodayDate()
{
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10)
        dd = '0' + dd;
    if (mm < 10)
        mm = '0' + mm;

    return dd + '-' + mm + '-' + yyyy;
}


//---------------------------------------------------------------------------------
//Routine di controllo
//---------------------------------------------------------------------------------

function controllaDataRegistrazione(campoDat)
{
    /**
     * La data registrazione è obbligatoria Il controllo sulla data
     * registrazione verificha che la data immessa cada all'interno di uno dei
     * mesi in linea. I mesi in linea coincidono con le date pianificate di
     * riporto saldo
     *
     */
    var datareg = $("#" + campoDat).val();

    if (isNotEmpty(datareg)) {

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    $("#" + campoDat + "_messaggio").html(xmlhttp.responseText);
                    $("#" + campoDat + "_control_group").addClass("has-error");
                } else {
                    $("#" + campoDat + "_messaggio").html("");
                    $("#" + campoDat + "_control_group").removeClass("has-error");
                }
            }
        };
        xmlhttp.open("GET", "../primanota/controllaDataRegistrazioneFacade.class.php?modo=start&datareg=" + datareg, true);
        xmlhttp.send();
    }
}

//---------------------------------------------------------------------------------

function controllaData(campoData)
{
    if (isNotEmpty($("#" + campoData).val())) {
        $("#" + campoData + "_control_group").removeClass("has-error");
        $("#" + campoData + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoData + "_control_group").addClass("has-error");
        $("#" + campoData + "_messaggio").html("obbligatorio");
        return false;
    }
}

//---------------------------------------------------------------------------------

function controllaCodice(campoCod)
{
    if (isNotEmpty($("#" + campoCod).val())) {
        $("#" + campoCod + "_control_group").removeClass("has-error");
        $("#" + campoCod + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoCod + "_control_group").addClass("has-error");
        $("#" + campoCod + "_messaggio").html("obbligatorio");
        return false;
    }
}

//---------------------------------------------------------------------------------

function controllaDescrizione(campoDes)
{
    if (isNotEmpty($("#" + campoDes).val())) {
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

function controllaCausale(campoCau)
{
    /**
     * La causale è obbligatoria
     */
    if (isNotEmpty($("#" + campoCau).val())) {
        $("#" + campoCau + "_control_group").removeClass("has-error");
        $("#" + campoCau + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoCau + "_control_group").addClass("has-error");
        $("#" + campoCau + "_messaggio").html("obbligatoria");
        return false;
    }
}

//---------------------------------------------------------------------

function controllaFornitore(campoForn)
{
    if (isEmpty($("#" + campoForn).val())) {
        $("#" + campoForn + "_control_group").addClass("has-error");        
    } else {
        $("#" + campoForn + "_control_group").removeClass("has-error");
    }
}

//---------------------------------------------------------------------

function controllaCliente(campoCli)
{
    if (isEmpty($("#" + campoCli).val())) {
        $("#" + campoCli + "_control_group").addClass("has-error");        
    } else {
        $("#" + campoCli + "_control_group").removeClass("has-error");
    }
}

//---------------------------------------------------------------------

function controllaClienteFornitore(campoForn, campoCli)
{
    /**
     * Il cliente e il fornitore sono mutualmente esclusivi Possono mancare
     * entrambi
     */
    if (isNotEmpty($("#" + campoForn).val()) && isNotEmpty($("#" + campoCli).val()))
    {
        $("#" + campoForn + "_control_group").addClass("has-error");
        $("#" + campoCli + "_control_group").addClass("has-error");
        return false;
    } else if (isEmpty($("#" + campoForn).val()) && isEmpty($("#" + campoCli).val())) {
        $("#" + campoForn + "_control_group").removeClass("has-error");
        $("#" + campoCli + "_control_group").removeClass("has-error");
        return true;
    } else {
        $("#" + campoForn + "_control_group").removeClass("has-error");
        $("#" + campoCli + "_control_group").removeClass("has-error");
        return true;
    }
}

//---------------------------------------------------------------------

function controllaNumeroFattura(campoFat)
{
    var numfatt = $("#" + campoFat).val();

    if (isNotEmpty(numfatt)) {
        $("#" + campoFat + "_control_group").removeClass("has-error");
        $("#" + campoFat + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoFat + "_control_group").addClass("has-error");
        $("#" + campoFat + "_messaggio").html("obbligatoria");
        return false;
    }
}

//---------------------------------------------------------------------

function validaNumeroFattura(campoCli, campoForn, campoFat, campoDat)
{
    var cliente = $("#" + campoCli).val();
    var fornitore = $("#" + campoForn).val();

    if (isNotEmpty(fornitore)) {
        controllaNumeroFatturaFornitore(campoForn, campoFat, campoDat);        
    } else if (isNotEmpty(cliente)) {
        controllaNumeroFatturaCliente(campoCli, campoFat, campoDat);
    }
}

//---------------------------------------------------------------------

function controllaNumeroFatturaFornitore(campoForn, campoFat, campoDat)
{
    var fornitore = $("#" + campoForn).val();
    var numfatt = $("#" + campoFat).val();
    var numfattOrig = $("#" + campoFat + "_orig").val();
    var datareg = $("#" + campoDat).val();

    if (isNotEmpty(numfatt) && isNotEmpty(datareg) && isNotEmpty(fornitore)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    if (numfatt !== numfattOrig) {
                        $("#" + campoDat + "_control_group").addClass("has-error");
                        $("#" + campoFat + "_control_group").addClass("has-error");
                        $("#" + campoForn + "_control_group").addClass("has-error");
                        $("#" + campoFat + "_messaggio").html(xmlhttp.responseText);
                    } else {
                        $("#" + campoDat + "_control_group").removeClass("has-error");
                        $("#" + campoFat + "_control_group").removeClass("has-error");
                        $("#" + campoForn + "_control_group").removeClass("has-error");
                        $("#" + campoFat + "_messaggio").html("");
                    }
                } else {
                    $("#" + campoDat + "_control_group").removeClass("has-error");
                    $("#" + campoFat + "_control_group").removeClass("has-error");
                    $("#" + campoForn + "_control_group").removeClass("has-error");
                    $("#" + campoFat + "_messaggio").html("");
                }
            }
        };
        xmlhttp.open("GET", "cercaFatturaFornitoreFacade.class.php?modo=start&fornitore_mod=" + fornitore + "&numfatt_mod=" + numfatt + "&datareg_mod=" + datareg, true);
        xmlhttp.send();
    } else
        return true;
}

//---------------------------------------------------------------------

function controllaNumeroFatturaCliente(campoCli, campoFat, campoDat)
{
    var cliente = $("#" + campoCli).val();
    var numfatt = $("#" + campoFat).val();
    var numfattOrig = $("#" + campoFat + "_orig").val();
    var datareg = $("#" + campoDat).val();
    
    if (isNotEmpty(numfatt) && isNotEmpty(datareg) && isNotEmpty(cliente)) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                if (isNotEmpty(xmlhttp.responseText)) {
                    if (numfatt !== numfattOrig) {
                        $("#" + campoDat + "_control_group").addClass("has-error");
                        $("#" + campoFat + "_control_group").addClass("has-error");
                        $("#" + campoCli + "_control_group").addClass("has-error");
                        $("#" + campoFat + "_messaggio").html(xmlhttp.responseText);
                    } else {
                        $("#" + campoDat + "_control_group").removeClass("has-error");
                        $("#" + campoFat + "_control_group").removeClass("has-error");
                        $("#" + campoCli + "_control_group").removeClass("has-error");
                        $("#" + campoFat + "_messaggio").html("");
                    }
                } else {
                    $("#" + campoDat + "_control_group").removeClass("has-error");
                    $("#" + campoFat + "_control_group").removeClass("has-error");
                    $("#" + campoCli + "_control_group").removeClass("has-error");
                    $("#" + campoFat + "_messaggio").html("");
                }
            }
        };
        xmlhttp.open("GET", "cercaFatturaClienteFacade.class.php?modo=start&cliente_mod=" + cliente + "&numfatt_mod=" + numfatt + "&datareg_mod=" + datareg, true);
        xmlhttp.send();
    } else
        return true;
}

//---------------------------------------------------------------------

function controllaDettagliRegistrazione(campoDet)
{
    /**
     * I dettagli della registrazione devono essere presenti e gli importi del
     * Dare e Avere suivari conti devono annularsi.
     * L'importo inserito sul conto principale (fornitore/cliente) deve quadrare con la
     * somma degli importi di tutte le scadenze
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
    xmlhttp.open("GET", "../primanota/verificaDettagliRegistrazioneFacade.class.php?modo=start&scadenzeTable=" + campoDet, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------

function controllaImporto(campoImp) {

    var importo = $("#" + campoImp).val();

    if (isNumeric(importo)) {
        $("#" + campoImp + "_control_group").removeClass("has-error");
        $("#" + campoImp + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoImp + "_control_group").addClass("has-error");
        $("#" + campoImp + "_messaggio").html("non valido");
        return false;
    }
}

//---------------------------------------------------------------------------------

function controllaNumero(campoNum) {

    var numero = $("#" + campoNum).val();

    if (isNumeric(numero)) {
        $("#" + campoNum + "_control_group").removeClass("has-error");
        $("#" + campoNum + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoNum + "_control_group").addClass("has-error");
        $("#" + campoNum + "_messaggio").html("non valido");
        return false;
    }
}

//---------------------------------------------------------------------------------

function controllaQuantita(campoQta) {

    var qta = $("#" + campoQta).val();

    if (isNumeric(qta)) {
        $("#" + campoQta + "_control_group").removeClass("has-error");
        $("#" + campoQta + "_messaggio").html("");
        return true;
    } else {
        $("#" + campoQta + "_control_group").addClass("has-error");
        $("#" + campoQta + "_messaggio").html("non valido");
        return false;
    }
}

function isNotEmpty(campo) {
    
    if ((campo !== "") && (campo !== " ")) {
        return true;
    }
    return false;
}

function isEmpty(campo) {
    
    if ((campo === "") || (campo === " ") || (campo === null)) {
        return true;
    }
    return false;
}