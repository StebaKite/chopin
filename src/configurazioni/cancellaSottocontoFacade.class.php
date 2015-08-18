<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'cancellaSottoconto.class.php';

session_start();

$cancellaSottoconto = CancellaSottoconto::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codsottoconto"] = $_POST["codsottoconto"];
	$cancellaSottoconto->start();
}

?>