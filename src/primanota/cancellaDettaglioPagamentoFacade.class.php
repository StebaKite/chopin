<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'cancellaDettaglioPagamento.class.php';

session_start();

$cancellaDettaglioPagamento = CancellaDettaglioPagamento::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idDettaglioRegistrazione"] = $_POST["idDettaglioRegistrazione"];	
	$cancellaDettaglioPagamento->go();
}

?>