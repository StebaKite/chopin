<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'bilancio.class.php';

session_start();

$bilancio = Bilancio::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["tipoBilancio"] = "Esercizio";
	$bilancio->start();
}
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = "01/01/" . $_REQUEST["anno_eserczio_sel"];  
	$_SESSION["datareg_a"] = "31/12/" . $_REQUEST["anno_eserczio_sel"];
	$_SESSION["anno_eserczio_sel"] = $_REQUEST["anno_eserczio_sel"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	
	$bilancio->go();
}

?>