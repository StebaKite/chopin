<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaMercato.class.php';

session_start();

$creaMercato = CreaMercato::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codmercato"] = $_REQUEST["codmercato"];
	$_SESSION["desmercato"] = $_REQUEST["desmercato"];
	$_SESSION["cittamercato"] = $_REQUEST["cittamercato"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	
	$creaMercato->go();
}

?>