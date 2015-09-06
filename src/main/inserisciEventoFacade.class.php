<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'inserisciEvento.class.php';

session_start();

$inserisciEvento = InserisciEvento::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["dataevento"] = $_REQUEST["dataevento"];
	$_SESSION["notaevento"] = $_REQUEST["notaevento"];

	$inserisciEvento->go();
}

?>