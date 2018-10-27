<?php

require_once 'cercaCodiceFornitore.class.php';

session_start();

$cercaCodiceFornitore = CercaCodiceFornitore::getInstance();

if ($_GET["modo"] == "start") {
	$_SESSION["codfornitore"] = $_REQUEST["codfornitore"];
	$cercaCodiceFornitore->start();
}

?>