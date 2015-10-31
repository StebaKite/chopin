<?php
set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'creaSaldo.class.php';

session_start();

$creaSaldo = CreaSaldo::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaSaldo->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codneg"] = $_REQUEST["codneg_sel"];	
	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["codsottoconto"] = $_REQUEST["codsottoconto"];
	$_SESSION["datsaldo"] = $_REQUEST["datsaldo"];
	$_SESSION["dessaldo"] = $_REQUEST["dessaldo"];
	$_SESSION["impsaldo"] = $_REQUEST["impsaldo"];
	$_SESSION["dareavere"] = $_REQUEST["dareavere"];
	
	$creaSaldo->go();
}

?>