<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'ricercaSaldo.class.php';

session_start();

$ricercaSaldo = RicercaSaldo::getInstance();

if ($_GET["modo"] == "start") $ricercaSaldo->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datarrip_saldo"] = $_REQUEST["datarrip_saldo"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];

	$ricercaSaldo->go();
}

?>