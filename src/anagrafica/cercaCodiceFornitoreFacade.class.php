<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'cercaCodiceFornitore.class.php';

session_start();

$cercaCodiceFornitore = CercaCodiceFornitore::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["codfornitore"] = $_REQUEST["codfornitore"];
	$cercaCodiceFornitore->start();
}

?>