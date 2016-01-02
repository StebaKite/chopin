<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'modificaCausale.class.php';

session_start();

$modificaCausale = ModificaCausale::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["codcausale"] = $_GET["codcausale"];

	$modificaCausale->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codcausale"] = $_REQUEST["codcausale"];
	$_SESSION["descausale"] = $_REQUEST["descausale"];
	$_SESSION["catcausale"] = $_REQUEST["catcausale"];
	
	$modificaCausale->go();
}

?>