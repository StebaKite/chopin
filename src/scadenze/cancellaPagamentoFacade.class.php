<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'cancellaPagamento.class.php';

session_start();

$cancellaPagamento = CancellaPagamento::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idScadenza"] = $_REQUEST["idScadenza"];
	$_SESSION["idPagamento"] = $_REQUEST["idPagamento"];
	$cancellaPagamento->start();
}

?>