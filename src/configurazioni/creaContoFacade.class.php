<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaConto.class.php';

session_start();

$creaConto = CreaConto::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaConto->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_POST["codconto"];
	$_SESSION["desconto"] = $_POST["desconto"];
	$_SESSION["catconto"] = $_POST["categoria"];
	$_SESSION["tipconto"] = $_POST["dareavere"];
	$_SESSION["sottocontiInseriti"] = $_POST["sottocontiInseriti"];
	$_SESSION["indexSottocontiInseriti"] = $_POST["indexSottocontiInseriti"];

	$creaConto->go();
}

?>