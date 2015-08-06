<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'ricercaRegistrazione.class.php';

$ricercaRegistrazione = RicercaRegistrazione::getInstance();

if ($_GET["modo"] == "start") $ricercaRegistrazione->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_POST["datareg_da"];
	$_SESSION["datareg_a"] = $_POST["datareg_a"];
	$_SESSION["numfatt"] = $_POST["numfatt"];
	
	$ricercaRegistrazione->go();
}
?>