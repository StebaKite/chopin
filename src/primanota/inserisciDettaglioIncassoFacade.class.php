<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'inserisciDettaglioIncasso.class.php';

session_start();

$inserisciDettaglioIncasso = InserisciDettaglioIncasso::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["importo"] = $_POST["importo"];
	$_SESSION["conti"] = $_POST["conti"];
	$_SESSION["dareavere"] = $_POST["dareavere"];

	$inserisciDettaglioIncasso->go();
}

?>
