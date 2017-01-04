<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'cancellaIncasso.class.php';

session_start();

$cancellaIncasso = CancellaIncasso::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idScadenza"] = $_REQUEST["idScadenza"];
	$_SESSION["idIncasso"] = $_REQUEST["idIncasso"];
	$cancellaIncasso->start();
}

?>