<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'cancellaConto.class.php';

session_start();

$cancellaConto = CancellaConto::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_POST["codconto"];
	$cancellaConto->start();
}

?>