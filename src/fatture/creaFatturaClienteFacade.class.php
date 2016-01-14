<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'creaFatturaCliente.class.php';

session_start();

$creaFatturaCliente = CreaFatturaCliente::getInstance();

if ($_GET["modo"] == "start") {

	$creaFatturaCliente->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["datafat"] = $_REQUEST["datafat"];
	$_SESSION["idcliente"] = $_REQUEST["cliente"];
	$_SESSION["tipoadd"] = $_REQUEST["tipoadd"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["numfat"] = $_REQUEST["numfat"];
	$_SESSION["ragsocbanca"] = $_REQUEST["ragsocbanca"];
	$_SESSION["ibanbanca"] = $_REQUEST["ibanbanca"];
	$_SESSION["tipofat"] = $_REQUEST["tipofat"];
	$_SESSION["cognomenomeassistito"] = $_REQUEST["assistito"];
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];
	
	$creaFatturaCliente->go();
}

?>