<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'modificaCliente.class.php';

session_start();

$modificaCliente = ModificaCliente::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["idcliente"] = $_GET["idcliente"];

	$modificaCliente->start();
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

	$modificaCliente->go();
}

?>