<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'creaFatturaAziendaConsortile.class.php';

session_start();

$creaFatturaAziendaConsortile = CreaFatturaAziendaConsortile::getInstance();

if ($_GET["modo"] == "start") {

	$creaFatturaAziendaConsortile->start();
}

if ($_GET["modo"] == "go") {

// 	$_SESSION["descreg"] = $_REQUEST["descreg"];
// 	$_SESSION["datascad"] = $_REQUEST["datascad"];
// 	$_SESSION["datareg"] = $_REQUEST["datareg"];
// 	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
// 	$_SESSION["codneg"] = $_REQUEST["codneg"];
// 	$_SESSION["causale"] = $_REQUEST["causale"];
// 	$_SESSION["fornitore"] = $_REQUEST["fornitore"];
// 	$_SESSION["cliente"] = $_REQUEST["cliente"];
	
	$creaFatturaAziendaConsortile->go();
}

?>