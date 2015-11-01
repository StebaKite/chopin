<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'cercaFatturaFornitore.class.php';

session_start();

$cercaFatturaFornitore = CercaFatturaFornitore::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["idfornitore"] = $_REQUEST["idfornitore"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];	
	$cercaFatturaFornitore->start();
}

?>