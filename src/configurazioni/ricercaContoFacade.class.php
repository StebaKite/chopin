<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'ricercaConto.class.php';

session_start();

$ricercaConto = RicercaConto::getInstance();

if ($_GET["modo"] == "start") $ricercaConto->start();
if ($_GET["modo"] == "go") {

	$_SESSION["categoria"] = $_POST["categoria"];
	$_SESSION["tipoconto"] = $_POST["tipoconto"];

	$ricercaConto->go();
}

?>