<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'inserisciDettaglioPagamento.class.php';

session_start();

$inserisciDettaglioPagamento = InserisciDettaglioPagamento::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["importo"] = $_POST["importo"];
	$_SESSION["conti"] = $_POST["conti"];
	$_SESSION["dareavere"] = $_POST["dareavere"];

	$inserisciDettaglioPagamento->go();
}

?>
