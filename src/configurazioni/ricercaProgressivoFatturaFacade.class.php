<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'ricercaProgressivoFattura.class.php';

session_start();

$ricercaProgressivoFattura = RicercaProgressivoFattura::getInstance();

if ($_GET["modo"] == "start") $ricercaProgressivoFattura->start();
if ($_GET["modo"] == "go") {

	$_SESSION["catcliente"] = $_REQUEST["catcliente"];
	
	$ricercaProgressivoFattura->go();
}

?>