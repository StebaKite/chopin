<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaCausale.class.php';

session_start();

$creaCausale = CreaCausale::getInstance();

if ($_GET["modo"] == "start") {
	
	$creaCausale->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codcausale"] = $_REQUEST["codcausale"];
	$_SESSION["descausale"] = $_REQUEST["descausale"];
	$_SESSION["catcausale"] = $_REQUEST["catcausale"];
	
	$creaCausale->go();
}

?>