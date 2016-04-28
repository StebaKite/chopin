<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'cancellaRegistrazione.class.php';

session_start();

$cancellaRegistrazione = CancellaRegistrazione::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$cancellaRegistrazione->start();
}

?>