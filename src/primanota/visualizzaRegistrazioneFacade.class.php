<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'visualizzaRegistrazione.class.php';

session_start();

$visualizzaRegistrazione = VisualizzaRegistrazione::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["idRegistrazione"] = $_GET["idRegistrazione"];

	$visualizzaRegistrazione->start();
}

?>