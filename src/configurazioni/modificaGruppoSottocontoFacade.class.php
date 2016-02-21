<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'modificaGruppoSottoconto.class.php';

session_start();

$modificaGruppoSottoconto = ModificaGruppoSottoconto::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["codsottoconto"] = $_REQUEST["codsottoconto"];
	$_SESSION["indgruppo"] = $_REQUEST["indgruppo"];
	$modificaGruppoSottoconto->go();
}

?>