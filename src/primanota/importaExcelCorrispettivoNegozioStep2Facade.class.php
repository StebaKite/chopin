<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'importaExcelCorrispettivoNegozioStep2.class.php';

session_start();

$importaExcelCorrispettivoNegozioStep2 = ImportaExcelCorrispettivoNegozioStep2::getInstance();

if ($_GET["modo"] == "go") {
	
	$_SESSION["mese"] = $_REQUEST["mese"];
	$_SESSION["anno"] = $_REQUEST["anno"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["contocassa"] = $_REQUEST["contocassa"];
	
	$importaExcelCorrispettivoNegozioStep2->go();
}

?>