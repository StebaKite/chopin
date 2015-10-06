<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'ricercaRegistrazione.class.php';

session_start();

$ricercaRegistrazione = RicercaRegistrazione::getInstance();

if ($_GET["modo"] == "start") $ricercaRegistrazione->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["cod_causale"] = "2100";			// corrispettivi
	
	$ricercaRegistrazione->go();
}
?>