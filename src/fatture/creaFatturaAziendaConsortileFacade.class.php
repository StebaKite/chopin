<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'creaFatturaAziendaConsortile.class.php';

session_start();

$creaFatturaAziendaConsortile = CreaFatturaAziendaConsortile::getInstance();

if ($_GET["modo"] == "start") {

	$creaFatturaAziendaConsortile->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["datafat"] = $_REQUEST["datafat"];
	$_SESSION["idcliente"] = $_REQUEST["cliente"];
	$_SESSION["tipoadd"] = $_REQUEST["tipoadd"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["numfat"] = $_REQUEST["numfat"];
	$_SESSION["ragsocbanca"] = $_REQUEST["ragsocbanca"];
	$_SESSION["ibanbanca"] = $_REQUEST["ibanbanca"];
	
	$creaFatturaAziendaConsortile->go();
}

?>