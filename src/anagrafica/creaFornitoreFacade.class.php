<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'creaFornitore.class.php';

session_start();

$creaFornitore = CreaFornitore::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaFornitore->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codfornitore"] = $_REQUEST["codfornitore"];
	$_SESSION["desfornitore"] = $_REQUEST["desfornitore"];
	$_SESSION["indfornitore"] = $_REQUEST["indfornitore"];
	$_SESSION["cittafornitore"] = $_REQUEST["cittafornitore"];
	$_SESSION["capfornitore"] = $_REQUEST["capfornitore"];
	$_SESSION["tipoaddebito"] = $_REQUEST["tipoaddebito"];
	$_SESSION["numggscadenzafattura"] = $_REQUEST["numggscadenzafattura"];
	
	$creaFornitore->go();
}

?>