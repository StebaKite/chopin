<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'creaRegistrazione.class.php';

session_start();

$creaRegistrazione = CreaRegistrazione::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaRegistrazione->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datascad"] = $_REQUEST["datascad"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["fornitore"] = $_REQUEST["fornitore"];
	$_SESSION["cliente"] = $_REQUEST["cliente"];	
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];	
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];
	
	$creaRegistrazione->go();
}

?>