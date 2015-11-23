<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'cancellaScadenzaRegistrazione.class.php';

session_start();

$cancellaScadenzaRegistrazione = CancellaScadenzaRegistrazione::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idScadenzaRegistrazione"] = $_REQUEST["idScadenzaRegistrazione"];	
	$cancellaScadenzaRegistrazione->go();
}

?>