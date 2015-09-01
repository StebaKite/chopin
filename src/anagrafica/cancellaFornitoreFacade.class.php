<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'cancellaFornitore.class.php';

session_start();

$cancellaFornitore = CancellaFornitore::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idfornitore"] = $_POST["idfornitore"];
	$_SESSION["codfornitoreselezionato"] = $_POST["codfornitoreselezionato"];
	$cancellaFornitore->start();
}

?>