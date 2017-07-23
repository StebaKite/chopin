//---------------------------------------------------------------------------------				
// Causali
//---------------------------------------------------------------------------------				

//---------------------------------------------------------------------------------
function configuraCausale(codCausale) {
//---------------------------------------------------------------------------------	
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");
    		$("#elencoContiConfigurati").html(datiPagina[0]);
    		$("#elencoContiDisponibili").html(datiPagina[1]);
        	
    		$( "#configura-causale-form" ).dialog( "open" );
        }
    }
    xmlhttp.open("GET", "configuraCausaleFacade.class.php?modo=start&codcausale_conf=" + codCausale, true);
    xmlhttp.send();
}

$( "#configura-causale-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 1100,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

//---------------------------------------------------------------------------------	
function modificaCausale(codCausale) {
//---------------------------------------------------------------------------------		
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");

    		$("#codcausale_mod").val(datiPagina[0]);
    		$("#descausale_mod").val(datiPagina[1]);
    		
    		if (datiPagina[2] == "GENERI") {
    			$("#generi_mod").prop("checked", true).button("refresh");
    		}
    		else {
        		if (datiPagina[2] == "INCPAG") {
        			$("#incpag_mod").prop("checked", true).button("refresh");
        		}
        		else {
            		if (datiPagina[2] == "CORRIS") {
            			$("#corris_mod").prop("checked", true).button("refresh");
            		}
        		}
    		}        	
    		$( "#modifica-causale-form" ).dialog( "open" );
        }
    }
    xmlhttp.open("GET", "modificaCausaleFacade.class.php?modo=start&codcausale_mod=" + codCausale, true);
    xmlhttp.send();				
}

$( "#modifica-causale-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 750,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#modificaCausale").submit();				
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

//---------------------------------------------------------------------------------		
// Creazione di una nuova causale
//---------------------------------------------------------------------------------		
$( "#nuova-causale" ).click(function( event ) {
	$("#nuova-causale-form").dialog("open");
	event.preventDefault();
});

$( "#nuova-causale-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 750,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#nuovaCausale").submit();				
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

//---------------------------------------------------------------------------------		
function cancellaCausale(codcausale) {
//---------------------------------------------------------------------------------			
	$( "#codcausale" ).val(codcausale);
	$( "#cancella-causale-form" ).dialog( "open" );
}

$( "#cancella-causale-form" ).dialog({
	autoOpen: false,
	modal: true,
	width: 300,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$(this).dialog('close');
				$("#cancellaCausale").submit();				
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

//---------------------------------------------------------------------------------			
function includiConto(codConto) {
//---------------------------------------------------------------------------------			
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var response = xmlhttp.responseText;
        	
        	var datiPagina = response.split("|");
    		$("#elencoContiConfigurati").html(datiPagina[0]);
    		$("#elencoContiDisponibili").html(datiPagina[1]);
        }
    }
    xmlhttp.open("GET", "includiContoCausaleFacade.class.php?modo=start&codconto=" + codConto, true);
    xmlhttp.send();
}

//---------------------------------------------------------------------------------			
function escludiConto(codConto) {
//---------------------------------------------------------------------------------			
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var response = xmlhttp.responseText;
      	
			var datiPagina = response.split("|");
			$("#elencoContiConfigurati").html(datiPagina[0]);
			$("#elencoContiDisponibili").html(datiPagina[1]);
		}
	}
	xmlhttp.open("GET", "escludiContoCausaleFacade.class.php?modo=start&codconto=" + codConto, true);
	xmlhttp.send();
}

//---------------------------------------------------------------------------------			

$( ".selectmenuCausaleRapido" )
	.selectmenu({change:
		function(){
			var causale = $("#causale").val();
			
			if (causale != "") {
	        	$( "#tdcausale").removeClass("inputFieldError");	
	            $( "#esitoCausale" ).val("");			
				$( "#messaggioControlloCausale" ).html("");
			}
			else {
				$("#messaggioControlloCausale").html("Dato errato");
				$("#tdcausale").addClass("inputFieldError");	
			}
		
			var xmlhttp = new XMLHttpRequest();
	        xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {}
	        }
	        xmlhttp.open("GET", "loadContiCausaleFacade.class.php?modo=start&causale=" + causale, true);
	        xmlhttp.send();			
		}
	})
	.selectmenu({width: 300})
	.selectmenu("menuWidget")
	.addClass("overflow");

//---------------------------------------------------------------------------------			
