<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'cercaFatturaCliente.class.php';

session_start();

$cercaFatturaCliente = CercaFatturaCliente::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["idcliente"] = $_REQUEST["idcliente"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];	
	$_SESSION["datareg"] = $_REQUEST["datareg"];	
	$cercaFatturaCliente->start();
}

?>