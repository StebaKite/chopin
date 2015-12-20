<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'cercaCfisCliente.class.php';

session_start();

$cercaCfisCliente = CercaCfisCliente::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["codfisc"] = $_REQUEST["codfisc"];
	$cercaCfisCliente->start();
}

?>