<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'estraPdfBilancio.class.php';

session_start();

$estraiPdfBilancio = EstraiPdfBilancio::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["catconto_sel"] = $_REQUEST["catconto_sel"];

	$estraiPdfBilancio->start();
}

?>