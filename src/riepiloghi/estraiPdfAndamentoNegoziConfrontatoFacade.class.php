<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'estraiPdfAndamentoNegoziConfrontato.class.php';

session_start();

$estraiPdfAndamentoNegoziConfrontato = EstraiPdfAndamentoNegoziConfrontato::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["datareg_da_rif"] = $_REQUEST["datareg_da_rif"];
	$_SESSION["datareg_a_rif"] = $_REQUEST["datareg_a_rif"];
	$_SESSION["codneg_sel_rif"] = $_REQUEST["codneg_sel_rif"];
	
	$estraiPdfAndamentoNegoziConfrontato->start();
}

?>