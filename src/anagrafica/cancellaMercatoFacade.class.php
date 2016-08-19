<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'cancellaMercato.class.php';

session_start();

$cancellaMercato = CancellaMercato::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idmercato"] = $_REQUEST["idmercato"];
	$_SESSION["codmercatoselezionato"] = $_REQUEST["codmercatoselezionato"];
	$cancellaMercato->start();
}

?>