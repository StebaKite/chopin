<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'bilancio.class.php';

session_start();

$bilancio = Bilancio::getInstance();

if ($_GET["modo"] == "start") {
	
	$_SESSION["tipoBilancio"] = "Periodico";
	$bilancio->start();	
}
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["saldiInclusi"] = $_REQUEST["saldiInclusi"];
	$_SESSION["soloContoEconomico"] = $_REQUEST["soloContoEconomico"];
	
	$bilancio->go();
}

?>