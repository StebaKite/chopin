<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'ricercaCausale.class.php';

session_start();

$ricercaCausale = RicercaCausale::getInstance();

if ($_GET["modo"] == "start") $ricercaCausale->start();
if ($_GET["modo"] == "go") {

	$_SESSION["causale"] = $_POST["causale"];

	$ricercaCausale->go();
}

?>