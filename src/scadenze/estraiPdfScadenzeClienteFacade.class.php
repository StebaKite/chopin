<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'estraiPdfScadenzeCliente.class.php';

session_start();

$estraiPdfScadenzeCliente = EstraiPdfScadenzeCliente::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datascad_da"] = $_REQUEST["datascad_da"];
	$_SESSION["datascad_a"] = $_REQUEST["datascad_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	
	$estraiPdfScadenzeCliente->start();
}

?>