<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'inserisciDettaglioRegistrazione.class.php';

session_start();

$inserisciDettaglioRegistrazione = InserisciDettaglioRegistrazione::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["importo"] = $_POST["importo"];
	$_SESSION["conti"] = $_POST["conti"];
	$_SESSION["dareavere"] = $_POST["dareavere"];
	
	$inserisciDettaglioRegistrazione->go();
}

?>
