<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'cancellaCliente.class.php';

session_start();

$cancellaCliente = CancellaCliente::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idcliente"] = $_POST["idcliente"];
	$_SESSION["codclienteselezionato"] = $_POST["codclienteselezionato"];
	$cancellaCliente->start();
}

?>