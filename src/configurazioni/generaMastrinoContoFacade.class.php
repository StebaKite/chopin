<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'generaMastrinoConto.class.php';

session_start();

$generaMastrinoConto = GeneraMastrinoConto::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_POST["codcontogenera"];
	$_SESSION["codsottoconto"] = $_POST["codsottocontogenera"];
	$_SESSION["datareg_da"] = $_POST["datareg_da"];
	$_SESSION["datareg_a"] = $_POST["datareg_a"];
	$generaMastrinoConto->go();
}

?>