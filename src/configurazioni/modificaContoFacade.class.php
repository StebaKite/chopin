<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'modificaConto.class.php';

session_start();

$modificaConto = ModificaConto::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["codconto"] = $_GET["codconto"];

	$modificaConto->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_POST["codconto"];
	$_SESSION["desconto"] = $_POST["desconto"];
	$_SESSION["catconto"] = $_POST["categoria"];
	$_SESSION["tipconto"] = $_POST["dareavere"];
	
	$modificaConto->go();
}

?>