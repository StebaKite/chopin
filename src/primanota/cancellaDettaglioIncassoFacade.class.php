<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'cancellaDettaglioIncasso.class.php';

session_start();

$cancellaDettaglioIncasso = CancellaDettaglioIncasso::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idDettaglioRegistrazione"] = $_POST["idDettaglioRegistrazione"];	
	$cancellaDettaglioIncasso->go();
}

?>