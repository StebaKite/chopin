<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'estraiPdfMastrinoConto.class.php';

session_start();

$estraiPdfMastrinoConto = EstraiPdfMastrinoConto::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["codsottoconto"] = $_REQUEST["codsottoconto"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["catconto"] = $_REQUEST["catconto"];
	$_SESSION["desconto"] = $_REQUEST["desconto"];
	$_SESSION["dessottoconto"] = $_REQUEST["dessottoconto"];
	
	$estraiPdfMastrinoConto->start();
}

?>