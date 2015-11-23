<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'cancellaDettaglioRegistrazione.class.php';

session_start();

$cancellaDettaglioRegistrazione = CancellaDettaglioRegistrazione::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idDettaglioRegistrazione"] = $_POST["idDettaglioRegistrazione"];	
	$cancellaDettaglioRegistrazione->go();
}

?>