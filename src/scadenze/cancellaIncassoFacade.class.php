<?php

require_once 'cancellaIncasso.class.php';

session_start();

$cancellaIncasso = CancellaIncasso::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idScadenza"] = $_REQUEST["idScadenza"];
	$_SESSION["idIncasso"] = $_REQUEST["idIncasso"];
	$cancellaIncasso->start();
}