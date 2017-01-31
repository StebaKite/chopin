<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'andamentoNegoziConfrontato.class.php';

session_start();

$andamentoNegoziConfrontato = AndamentoNegoziConfrontato::getInstance();

if ($_GET["modo"] == "start") {

	$andamentoNegoziConfrontato->start();
}
if ($_GET["modo"] == "go") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["codneg_sel_rif"] = $_REQUEST["codneg_sel_rif"];
	$_SESSION["datareg_da_rif"] = $_REQUEST["datareg_da_rif"];
	$_SESSION["datareg_a_rif"] = $_REQUEST["datareg_a_rif"];
	
	$andamentoNegoziConfrontato->go();
}

?>