<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'ricercaScadenzeAperteFornitore.class.php';

session_start();

$ricercaScadenzeAperteFornitore = RicercaScadenzeAperteFornitore::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["desforn"] = $_REQUEST["desforn"];
	$ricercaScadenzeAperteFornitore->start();
}

?>