<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaConto.class.php';

session_start();

$creaConto = CreaConto::getInstance();

if ($_GET["modo"] == "start") {

	unset($_SESSION["codconto"]);
	unset($_SESSION["desconto"]);
	unset($_SESSION["catconto"]);
	unset($_SESSION["tipconto"]);
	unset($_SESSION["indpresenza"]);
	unset($_SESSION["indvissottoconti"]);
	unset($_SESSION["indclassificazione"]);

	unset($_SESSION["numrigabilancio"]);
	unset($_SESSION["sottocontiInseriti"]);
	unset($_SESSION["indexSottocontiInseriti"]);
	
	$creaConto->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["desconto"] = $_REQUEST["desconto"];
	$_SESSION["catconto"] = $_REQUEST["categoria"];
	$_SESSION["tipconto"] = $_REQUEST["dareavere"];
	$_SESSION["indpresenza"] = $_REQUEST["indpresenza"];
	$_SESSION["indvissottoconti"] = $_REQUEST["indvissottoconti"];
	$_SESSION["indclassificazione"] = $_REQUEST["indclassificazione"];
	
	$_SESSION["numrigabilancio"] = $_REQUEST["numrigabilancio"];		
	$_SESSION["sottocontiInseriti"] = $_POST["sottocontiInseriti"];
	$_SESSION["indexSottocontiInseriti"] = $_POST["indexSottocontiInseriti"];

	$creaConto->go();
}

?>