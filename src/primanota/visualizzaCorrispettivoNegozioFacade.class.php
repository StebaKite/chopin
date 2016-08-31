<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'visualizzaCorrispettivoNegozio.class.php';

session_start();
$visualizzaCorrispettivoNegozio = VisualizzaCorrispettivoNegozio::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];
	$visualizzaCorrispettivoNegozio->start();
}

?>