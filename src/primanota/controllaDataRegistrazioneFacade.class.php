<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'controllaDataRegistrazione.class.php';

session_start();

$controllaDataRegistrazione = ControllaDataRegistrazione::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$controllaDataRegistrazione->start();
}

?>