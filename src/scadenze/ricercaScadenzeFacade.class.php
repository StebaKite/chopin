<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'ricercaScadenze.class.php';

session_start();

$ricercaScadenze = RicercaScadenze::getInstance();

if ($_GET["modo"] == "start") $ricercaScadenze->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datascad_da"] = $_REQUEST["datascad_da"];
	$_SESSION["datascad_a"] = $_REQUEST["datascad_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["statoscad_sel"] = $_REQUEST["statoscad_sel"];
	
	$ricercaScadenze->go();
}

?>