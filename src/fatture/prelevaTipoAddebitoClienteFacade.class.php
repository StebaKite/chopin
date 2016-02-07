<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'prelevaTipoAddebitoCliente.class.php';

session_start();

$prelevaTipoAddebitoCliente = PrelevaTipoAddebitoCliente::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["descliente"] = $_REQUEST["descliente"];
	$prelevaTipoAddebitoCliente->start();
}

?>