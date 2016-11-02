<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'creaIncasso.class.php';

session_start();

$creaIncasso = CreaIncasso::getInstance();

if ($_GET["modo"] == "start") {
	
	unset($_SESSION["descreg"]);
	unset($_SESSION["numfatt"]);
	unset($_SESSION["codneg"]);
	unset($_SESSION["causale"]);
	unset($_SESSION["cliente"]);

	unset($_SESSION["esitoControlloDescrizione"]);
	unset($_SESSION["esitoControlloCausale"]);
	unset($_SESSION["esitoControlloNegozio"]);
	unset($_SESSION["esitoControlloCliente"]);
	unset($_SESSION["esitoNumfatt"]);
	unset($_SESSION["esitoControlloDataRegistrazione"]);
	
	unset($_SESSION["dettagliInseriti"]);
	unset($_SESSION["indexDettagliInseriti"]);
	
	$creaIncasso->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["cliente"] = $_REQUEST["cliente"];

	$_SESSION["esitoDescrizione"] = $_REQUEST["esitoDescrizione"];
	$_SESSION["esitoNumeroFattura"] = $_REQUEST["esitoNumeroFattura"];
	$_SESSION["esitoCausale"] = $_REQUEST["esitoCausale"];
	$_SESSION["esitoNegozio"] = $_REQUEST["esitoNegozio"];
	$_SESSION["esitoCliente"] = $_REQUEST["esitoCliente"];
	$_SESSION["esitoNumfatt"] = $_REQUEST["esitoNumfatt"];
	$_SESSION["esitoControlloDataRegistrazione"] = $_REQUEST["esitoControlloDataRegistrazione"];
	
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];

	$creaIncasso->go();
}

?>