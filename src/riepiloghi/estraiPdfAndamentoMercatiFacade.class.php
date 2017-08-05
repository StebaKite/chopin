<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'estraiPdfAndamentoMercati.class.php';

session_start();
xdebug_disable();

$estraiPdfAndamentoMercati = EstraiPdfAndamentoMercati::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];

	$estraiPdfAndamentoMercati->start();
}

?>