<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'estraiPdfScadenze.class.php';

session_start();

$estraiPdfScadenze = EstraiPdfScadenze::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$estraiPdfScadenze->start();
}

?>