<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'modificaFornitore.class.php';

session_start();

$modificaFornitore = ModificaFornitore::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["idfornitore"] = $_GET["idfornitore"];

	$modificaFornitore->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["codfornitore"] = $_POST["codfornitore"];
	$_SESSION["desfornitore"] = $_POST["desfornitore"];
	$_SESSION["indfornitore"] = $_POST["indfornitore"];
	$_SESSION["cittafornitore"] = $_POST["cittafornitore"];
	$_SESSION["capfornitore"] = $_POST["capfornitore"];
	$_SESSION["tipoaddebito"] = $_POST["tipoaddebito"];
	$_SESSION["numggscadenzafattura"] = $_POST["numggscadenzafattura"];

	$modificaFornitore->go();
}

?>