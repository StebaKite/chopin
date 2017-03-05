<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'andamentoMercati.class.php';

session_start();

$andamentoMercati = AndamentoMercati::getInstance();

if ($_GET["modo"] == "start") {

	$andamentoMercati->start();
}
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];

	$andamentoMercati->go();
}

?>