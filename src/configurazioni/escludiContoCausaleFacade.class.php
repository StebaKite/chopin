<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'escludiContoCausale.class.php';

session_start();

$escludiContoCausale = EscludiContoCausale::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["codconto"] = $_GET["codconto"];
	$escludiContoCausale->start();
}

?>