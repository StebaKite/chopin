<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'generaMastrinoConto.class.php';

session_start();

$generaMastrinoConto = GeneraMastrinoConto::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_REQUEST["codcontogenera"];
	$_SESSION["codsottoconto"] = $_REQUEST["codsottocontogenera"];
	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
	$_SESSION["saldiInclusi"] = $_REQUEST["saldiInclusi"];
	$generaMastrinoConto->go();
}

?>