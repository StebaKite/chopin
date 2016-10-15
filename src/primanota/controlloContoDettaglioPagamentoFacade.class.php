<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'controlloContoDettaglioPagamento.class.php';

session_start();

$controlloContoDettaglioPagamento = ControlloContoDettaglioPagamento::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["fornitore"] = $_REQUEST["fornitore"];		// qui c'è la descrizione del fornitore
	
	$conto = explode(" - ", $_REQUEST["conto"]);			// qui c'è il conto-sottoconto e la descrizione	
	$_SESSION["conto"] = $conto[0];
	
	$controlloContoDettaglioPagamento->start();
}

?>