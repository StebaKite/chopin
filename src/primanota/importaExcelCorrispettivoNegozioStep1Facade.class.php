<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'importaExcelCorrispettivoNegozioStep1.class.php';

session_start();

$importaExcelCorrispettivoNegozioStep1 = ImportaExcelCorrispettivoNegozioStep1::getInstance();

if ($_GET["modo"] == "start") {

	unset($_SESSION["mese"]);
	unset($_SESSION["anno"]);
	unset($_SESSION["codneg"]);
	unset($_SESSION["file"]);
	unset($_SESSION["datada"]);
	unset($_SESSION["dataa"]);
	
	$importaExcelCorrispettivoNegozioStep1->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["mese"] = $_REQUEST["mese"];
	$_SESSION["anno"] = $_REQUEST["anno"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	if ($_REQUEST["file"] != "") $_SESSION["file"] = $_REQUEST["file"];
	$_SESSION["datada"]= $_REQUEST["datada"];
	$_SESSION["dataa"] = $_REQUEST["dataa"];
	$_SESSION["contocassa"] = $_REQUEST["contocassa"];
	
	$importaExcelCorrispettivoNegozioStep1->go();
}

?>