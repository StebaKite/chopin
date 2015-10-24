<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'modificaConto.class.php';

session_start();

$modificaConto = ModificaConto::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["codconto"] = $_REQUEST["codconto"];

	$modificaConto->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["desconto"] = $_REQUEST["desconto"];
	$_SESSION["catconto"] = $_REQUEST["categoria"];
	$_SESSION["tipconto"] = $_REQUEST["dareavere"];
	$_SESSION["indpresenza"] = $_REQUEST["indpresenza"];
	$_SESSION["indclassificazione"] = $_REQUEST["indclassificazione"];
	
	$_SESSION["indvissottoconti"] = $_REQUEST["indvissottoconti"];
	$_SESSION["numrigabilancio"] = $_REQUEST["numrigabilancio"];
	
	$modificaConto->go();
}

?>