<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'creaRegistrazione.class.php';

session_start();

$creaRegistrazione = CreaRegistrazione::getInstance();

if ($_GET["modo"] == "start") {

	unset($_SESSION["descreg"]);
	unset($_SESSION["datascad"]);
	unset($_SESSION["numfatt"]);
	unset($_SESSION["codneg"]);
	unset($_SESSION["causale"]);
	unset($_SESSION["fornitore"]);
	unset($_SESSION["cliente"]);
	unset($_SESSION["esitoNumeroFattura"]);
	unset($_SESSION["dettagliInseriti"]);
	unset($_SESSION["indexDettagliInseriti"]);
	unset($_SESSION["scadenzeInserite"]);
	unset($_SESSION["indexScadenzeInserite"]);
	
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
	$_SESSION["esitoNumeroFattura"] = $_REQUEST["esitoNumeroFattura"];
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];	
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];
	$_SESSION["scadenzeInserite"] = $_REQUEST["scadenzeInserite"];
	$_SESSION["indexScadenzeInserite"] = $_REQUEST["indexScadenzeInserite"];
	
	$creaRegistrazione->go();
}

?>