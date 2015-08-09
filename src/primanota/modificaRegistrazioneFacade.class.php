<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'modificaRegistrazione.class.php';

session_start();

$modificaRegistrazione = ModificaRegistrazione::getInstance();

if ($_GET["modo"] == "start") {
	
	$_SESSION["idRegistrazione"] = $_GET["idRegistrazione"];
	
	$modificaRegistrazione->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["descreg"] = $_POST["descreg"];
	$_SESSION["datascad"] = $_POST["datascad"];
	$_SESSION["datareg"] = $_POST["datareg"];
	$_SESSION["numfatt"] = $_POST["numfatt"];
	$_SESSION["causale"] = $_POST["causale"];
	$_SESSION["fornitore"] = $_POST["fornitore"];
	$_SESSION["cliente"] = $_POST["cliente"];

	$modificaRegistrazione->go();
}

?>