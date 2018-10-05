<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'controlloContoDettaglioIncasso.class.php';

session_start();

$controlloContoDettaglioIncasso = ControlloContoDettaglioIncasso::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["cliente"] = $_REQUEST["cliente"];		// qui c'è la descrizione del cliente
	
	$conto = explode(" - ", $_REQUEST["conto"]);		// qui c'è il conto-sottoconto e la descrizione	
	$_SESSION["conto"] = $conto[0];
	
	$controlloContoDettaglioIncasso->start();
}

?>