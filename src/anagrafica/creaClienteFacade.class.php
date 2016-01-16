<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaCliente.class.php';

session_start();

$creaCliente = CreaCliente::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaCliente->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codcliente"] = $_REQUEST["codcliente"];
	$_SESSION["descliente"] = $_REQUEST["descliente"];
	$_SESSION["indcliente"] = $_REQUEST["indcliente"];
	$_SESSION["cittacliente"] = $_REQUEST["cittacliente"];
	$_SESSION["capcliente"] = $_REQUEST["capcliente"];
	$_SESSION["tipoaddebito"] = $_REQUEST["tipoaddebito"];
	$_SESSION["codpiva"] = $_REQUEST["codpiva"];
	$_SESSION["codfisc"] = $_REQUEST["codfisc"];
	$_SESSION["catcliente"] = $_REQUEST["catcliente"];
	$_SESSION["esitoPivaCliente"] = $_REQUEST["esitoPivaCliente"];
	$_SESSION["esitoCfisCliente"] = $_REQUEST["esitoCfisCliente"];
	
	$creaCliente->go();
}

?>