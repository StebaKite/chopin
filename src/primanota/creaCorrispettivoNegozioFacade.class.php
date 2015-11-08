<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'creaCorrispettivoNegozio.class.php';

session_start();

$creaCorrispettivoNegozio = CreaCorrispettivoNegozio::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaCorrispettivoNegozio->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];

	$creaCorrispettivoNegozio->go();
}

?>