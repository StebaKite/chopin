<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'ricercaScadenzeCliente.class.php';

session_start();

$ricercaScadenzeCliente = RicercaScadenzeCliente::getInstance();

if ($_GET["modo"] == "start") $ricercaScadenzeCliente->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["statoscad_sel"] = $_REQUEST["statoscad_sel"];
	
	$ricercaScadenzeCliente->go();
}

?>