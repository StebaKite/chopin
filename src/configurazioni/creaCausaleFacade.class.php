<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaCausale.class.php';

session_start();

$creaCausale = CreaCausale::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaCausale->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codcausale"] = $_POST["codcausale"];
	$_SESSION["descausale"] = $_POST["descausale"];

	$creaCausale->go();
}

?>