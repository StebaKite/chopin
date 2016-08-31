<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'leggiMercatiNegozio.class.php';

session_start();

$leggiMercatiNegozio = LeggiMercatiNegozio::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["negozio"] = $_REQUEST["negozio"];
	$leggiMercatiNegozio->start();
}

?>