<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'ricercaCliente.class.php';

session_start();

$ricercaCliente = RicercaCliente::getInstance();

if ($_GET["modo"] == "start") $ricercaCliente->start();
if ($_GET["modo"] == "go") {

	$_SESSION["codcliente"] = $_POST["codcliente"];

	$ricercaCliente->go();
}

?>