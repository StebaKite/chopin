<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'modificaMercato.class.php';

session_start();

$modificaMercato = ModificaMercato::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idmercato"] = $_REQUEST["idmercato_mod"];
	$_SESSION["codmercato"] = $_REQUEST["codmercato_mod"];
	$_SESSION["desmercato"] = $_REQUEST["desmercato_mod"];
	$_SESSION["cittamercato"] = $_REQUEST["cittamercato_mod"];
	
	$modificaMercato->go();
}

?>