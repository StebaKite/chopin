<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'prelevaProgressivoFattura.class.php';

session_start();

$prelevaProgressivoFattura = PrelevaProgressivoFattura::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["catcliente"] = $_REQUEST["catcliente"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];	
	$prelevaProgressivoFattura->start();
}

?>