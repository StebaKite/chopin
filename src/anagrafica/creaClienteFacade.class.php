<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'creaCliente.class.php';

session_start();

$creaCliente = CreaCliente::getInstance();

if ($_GET["modo"] == "start") {
	session_unset();
	$creaCliente->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codcliente"] = $_POST["codcliente"];
	$_SESSION["descliente"] = $_POST["descliente"];
	$_SESSION["indcliente"] = $_POST["indcliente"];
	$_SESSION["cittacliente"] = $_POST["cittacliente"];
	$_SESSION["capcliente"] = $_POST["capcliente"];
	$_SESSION["tipoaddebito"] = $_POST["tipoaddebito"];

	$creaCliente->go();
}

?>