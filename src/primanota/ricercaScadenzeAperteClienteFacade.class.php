<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'ricercaScadenzeAperteCliente.class.php';

session_start();

$ricercaScadenzeAperteCliente = RicercaScadenzeAperteCliente::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["idcliente"] = $_REQUEST["idcliente"];
	$ricercaScadenzeAperteCliente->start();
}

?>