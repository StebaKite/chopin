<?php

//set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'inserisciScadenzaRegistrazione.class.php';

session_start();

$inserisciScadenzaRegistrazione = InserisciScadenzaRegistrazione::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];
	$_SESSION["importosuppl"] = $_REQUEST["importosuppl"];
	$_SESSION["datascadsuppl"] = $_REQUEST["datascadsuppl"];
	
	$inserisciScadenzaRegistrazione->go();
}

?>
