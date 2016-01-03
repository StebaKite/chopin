<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'creaFatturaAziendaConsortile.class.php';

session_start();

$creaFatturaAziendaConsortile = CreaFatturaAziendaConsortile::getInstance();

if ($_GET["modo"] == "start") {

	$creaFatturaAziendaConsortile->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["idcliente"] = $_REQUEST["cliente"];
	$_SESSION["datafat"] = $_REQUEST["datafat"];
	$_SESSION["descfat"] = $_REQUEST["descfat"];
	$_SESSION["numfat"] = $_REQUEST["numfat"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["impofat"] = $_REQUEST["impofat"];
	$_SESSION["impivafat"] = $_REQUEST["impivafat"];
	
	$creaFatturaAziendaConsortile->go();
}

?>