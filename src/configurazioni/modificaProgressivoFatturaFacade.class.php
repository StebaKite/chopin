<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'modificaProgressivoFattura.class.php';

session_start();

$modificaProgressivoFattura = ModificaProgressivoFattura::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["catcliente"] = $_REQUEST["catcliente"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$modificaProgressivoFattura->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["catcliente"] = $_REQUEST["catcliente"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["notatesta"] = $_REQUEST["notatesta"];
	$_SESSION["notapiede"] = $_REQUEST["notapiede"];
	
	$modificaProgressivoFattura->go();
}

?>