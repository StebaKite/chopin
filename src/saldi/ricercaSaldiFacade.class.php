<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'ricercaSaldi.class.php';

session_start();

$ricercaSaldi = RicercaSaldi::getInstance();

if ($_GET["modo"] == "start") $ricercaSaldi->start();
if ($_GET["modo"] == "go") {

	$_SESSION["datarip_saldo"] = $_REQUEST["datarip_saldo"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];

	$ricercaSaldi->go();
}

?>