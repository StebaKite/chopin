<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/strumenti:/var/www/html/chopin/src/utility');
require_once 'cambiaContoStep1.class.php';

session_start();

$cambiaContoStep1 = CambiaContoStep1::getInstance();

if ($_GET["modo"] == "start") $cambiaContoStep1->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["conto_sel"] = $_REQUEST["conto_sel"];
	
	$cambiaContoStep1->go();
}
?>