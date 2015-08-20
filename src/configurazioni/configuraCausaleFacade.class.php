<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'configuraCausale.class.php';

session_start();

$configuraCausale = ConfiguraCausale::getInstance();

if ($_GET["modo"] == "start") {
	
	$_SESSION["codcausale"] = $_GET["codcausale"];
	$_SESSION["descausale"] = $_GET["descausale"];
	$configuraCausale->start();
}

?>