<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'leggiContiCausale.class.php';

session_start();

$leggiContiCausale = LeggiContiCausale::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["causale"] = $_GET["causale"];
	$leggiContiCausale->start();
}

?>