<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'chiudiEvento.class.php';

session_start();

$chiudiEvento = ChiudiEvento::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idevento"] = $_REQUEST["idevento"];
	$_SESSION["staevento"] = $_REQUEST["staevento"];
	
	$chiudiEvento->go();
}

?>