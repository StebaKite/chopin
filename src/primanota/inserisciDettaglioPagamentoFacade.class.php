<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'inserisciDettaglioPagamento.class.php';

session_start();

$inserisciDettaglioPagamento = InserisciDettaglioPagamento::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idPagamento"] = $_REQUEST["idPagamento"];
	$_SESSION["importo"] = $_REQUEST["importo"];
	$_SESSION["conti"] = $_REQUEST["conti"];
	$_SESSION["dareavere"] = $_REQUEST["dareavere"];

	$inserisciDettaglioPagamento->go();
}

?>
