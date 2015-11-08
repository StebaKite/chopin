<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'modificaIncasso.class.php';

session_start();

$modificaIncasso = ModificaIncasso::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];
	$_SESSION["idIncasso"] = $_REQUEST["idIncasso"];
	
	$modificaIncasso->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];
	$_SESSION["idIncasso"] = $_REQUEST["idIncasso"];
	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["cliente"] = $_REQUEST["cliente"];

	$modificaIncasso->go();
}

?>