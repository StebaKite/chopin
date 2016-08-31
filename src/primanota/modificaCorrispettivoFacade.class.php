<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'modificaCorrispettivo.class.php';

session_start();

$modificaCorrispettivo = ModificaCorrispettivo::getInstance();

if ($_GET["modo"] == "start") {
	
	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];	
	$modificaCorrispettivo->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["idmercato"] = $_REQUEST["mercati"];
	$_SESSION["causale"] = $_REQUEST["causale"];

	$modificaCorrispettivo->go();
}

?>