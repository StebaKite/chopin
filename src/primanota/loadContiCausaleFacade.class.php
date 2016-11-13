<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'loadContiCausale.class.php';

session_start();

$loadContiCausale = LoadContiCausale::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["desconto"] = $_REQUEST["desconto"];
	$loadContiCausale->start();
}

?>