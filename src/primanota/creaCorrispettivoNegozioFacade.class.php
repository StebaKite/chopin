<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'creaCorrispettivoNegozio.class.php';

session_start();

$creaCorrispettivoNegozio = CreaCorrispettivoNegozio::getInstance();

if ($_GET["modo"] == "start") {
	
	unset($_SESSION["descreg"]);
	unset($_SESSION["codneg"]);
	unset($_SESSION["causale"]);

	unset($_SESSION["esitoControlloDescrizione"]);
	unset($_SESSION["esitoControlloCausale"]);
	unset($_SESSION["esitoControlloNegozio"]);
	unset($_SESSION["esitoControlloDataRegistrazione"]);
	
	unset($_SESSION["dettagliInseriti"]);
	unset($_SESSION["indexDettagliInseriti"]);
	
	$creaCorrispettivoNegozio->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];

	$_SESSION["esitoDescrizione"] = $_REQUEST["esitoDescrizione"];
	$_SESSION["esitoNumeroFattura"] = $_REQUEST["esitoNumeroFattura"];
	$_SESSION["esitoCausale"] = $_REQUEST["esitoCausale"];
	$_SESSION["esitoNegozio"] = $_REQUEST["esitoNegozio"];
	$_SESSION["esitoControlloDataRegistrazione"] = $_REQUEST["esitoControlloDataRegistrazione"];
	
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];

	$creaCorrispettivoNegozio->go();
}

?>