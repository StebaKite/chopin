<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'cercaPivaCliente.class.php';

session_start();

$cercaPivaCliente = CercaPivaCliente::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["codpiva"] = $_REQUEST["codpiva"];
	$cercaPivaCliente->start();
}

?>