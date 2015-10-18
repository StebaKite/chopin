<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaConto.class.php';

session_start();

$creaConto = CreaConto::getInstance();

if ($_GET["modo"] == "start") {

	$creaConto->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["desconto"] = $_REQUEST["desconto"];
	$_SESSION["catconto"] = $_REQUEST["categoria"];
	$_SESSION["tipconto"] = $_REQUEST["dareavere"];
	$_SESSION["indpresenza"] = $_REQUEST["indpresenza"];
	$_SESSION["indvissottoconti"] = $_REQUEST["indvissottoconti"];
	$_SESSION["numrigabilancio"] = $_REQUEST["numrigabilancio"];		
	$_SESSION["sottocontiInseriti"] = $_POST["sottocontiInseriti"];
	$_SESSION["indexSottocontiInseriti"] = $_POST["indexSottocontiInseriti"];

	$creaConto->go();
}

?>