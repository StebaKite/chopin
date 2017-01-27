<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'importaExcelCorrispettivoMercatoStep1.class.php';

session_start();

$importaExcelCorrispettivoMercatoStep1 = ImportaExcelCorrispettivoMercatoStep1::getInstance();

if ($_GET["modo"] == "start") {

	unset($_SESSION["mese"]);
	unset($_SESSION["anno"]);
	unset($_SESSION["codneg"]);
	unset($_SESSION["mercati"]);
	unset($_SESSION["file"]);
	unset($_SESSION["datada"]);
	unset($_SESSION["dataa"]);

	$importaExcelCorrispettivoMercatoStep1->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["mese"] = $_REQUEST["mese"];
	$_SESSION["anno"] = $_REQUEST["anno"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["mercati"] = $_REQUEST["mercati"];
	if ($_REQUEST["file"] != "") $_SESSION["file"] = $_REQUEST["file"];
	$_SESSION["datada"]= $_REQUEST["datada"];
	$_SESSION["dataa"] = $_REQUEST["dataa"];

	$importaExcelCorrispettivoMercatoStep1->go();
}

?>