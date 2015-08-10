<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'creaRegistrazione.class.php';

session_start();

$creaRegistrazione = CreaRegistrazione::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaRegistrazione->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["descreg"] = $_POST["descreg"];
	$_SESSION["datascad"] = $_POST["datascad"];
	$_SESSION["datareg"] = $_POST["datareg"];
	$_SESSION["numfatt"] = $_POST["numfatt"];
	$_SESSION["causale"] = $_POST["causale"];
	$_SESSION["fornitore"] = $_POST["fornitore"];
	$_SESSION["cliente"] = $_POST["cliente"];	
	$_SESSION["dettagliInseriti"] = $_POST["dettagliInseriti"];	
	$_SESSION["indexDettagliInseriti"] = $_POST["indexDettagliInseriti"];
	
	$creaRegistrazione->go();
}

?>