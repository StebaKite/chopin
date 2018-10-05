<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'aggiungiClienteDettagliRegistrazione.class.php';

session_start();

$aggiungiClienteDettagliRegistrazione = AggiungiClienteDettagliRegistrazione::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["descliente"] = $_REQUEST["descliente"];
	$aggiungiClienteDettagliRegistrazione->start();
}

?>