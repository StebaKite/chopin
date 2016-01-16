<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'controlloCorrispettivo.class.php';

session_start();

$controlloCorrispettivo = ControlloCorrispettivo::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["conto"] = $_REQUEST["conto"];
	$_SESSION["importo"] = $_REQUEST["importo"];
	$controlloCorrispettivo->start();
}

?>