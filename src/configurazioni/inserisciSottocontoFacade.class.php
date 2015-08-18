<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'inserisciSottoconto.class.php';

session_start();

$inserisciSottoconto = InserisciSottoconto::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codsottoconto"] = $_POST["codsottoconto"];
	$_SESSION["dessottoconto"] = $_POST["dessottoconto"];

	$inserisciSottoconto->go();
}

?>