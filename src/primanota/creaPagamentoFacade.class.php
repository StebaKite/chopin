<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'creaPagamento.class.php';

session_start();

$creaPagamento = CreaPagamento::getInstance();

if ($_GET["modo"] == "start") {

	unset($_SESSION["descreg"]);
	unset($_SESSION["datareg"]);
	unset($_SESSION["numfatt"]);
	unset($_SESSION["codneg"]);
	unset($_SESSION["causale"]);
	unset($_SESSION["fornitore"]);
	unset($_SESSION["dettagliInseriti"]);
	unset($_SESSION["indexDettagliInseriti"]);
	
	$creaPagamento->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["fornitore"] = $_REQUEST["fornitore"];
	$_SESSION["dettagliInseriti"] = $_REQUEST["dettagliInseriti"];
	$_SESSION["indexDettagliInseriti"] = $_REQUEST["indexDettagliInseriti"];

	$creaPagamento->go();
}

?>