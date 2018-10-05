<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'inserisciDettaglioIncasso.class.php';

session_start();

$inserisciDettaglioIncasso = InserisciDettaglioIncasso::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idIncasso"] = $_REQUEST["idIncasso"];
	$_SESSION["importo"] = $_REQUEST["importo"];
	$_SESSION["conti"] = $_REQUEST["conti"];
	$_SESSION["dareavere"] = $_REQUEST["dareavere"];

	$inserisciDettaglioIncasso->go();
}

?>
