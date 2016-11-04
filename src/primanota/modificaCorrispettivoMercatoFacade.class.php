<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'modificaCorrispettivoMercato.class.php';

session_start();

$modificaCorrispettivoMercato = ModificaCorrispettivoMercato::getInstance();

if ($_GET["modo"] == "start") {
	
	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];	

	unset($_SESSION["esitoControlloDescrizione"]);
	unset($_SESSION["esitoControlloCausale"]);
	unset($_SESSION["esitoControlloMercato"]);
	unset($_SESSION["esitoControlloNegozio"]);
	unset($_SESSION["esitoControlloDataRegistrazione"]);
	
	$modificaCorrispettivoMercato->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["idmercato"] = $_REQUEST["mercati"];
	$_SESSION["causale"] = $_REQUEST["causale"];

	$_SESSION["esitoDescrizione"] = $_REQUEST["esitoDescrizione"];
	$_SESSION["esitoNumeroFattura"] = $_REQUEST["esitoNumeroFattura"];
	$_SESSION["esitoCausale"] = $_REQUEST["esitoCausale"];
	$_SESSION["esitoMercato"] = $_REQUEST["esitoMercato"];
	$_SESSION["esitoNegozio"] = $_REQUEST["esitoNegozio"];
	$_SESSION["esitoControlloDataRegistrazione"] = $_REQUEST["esitoControlloDataRegistrazione"];
	
	$modificaCorrispettivoMercato->go();
}

?>