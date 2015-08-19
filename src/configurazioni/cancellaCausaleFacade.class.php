<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'cancellaCausale.class.php';

session_start();

$cancellaCausale = CancellaCausale::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["codcausale"] = $_POST["codcausale"];
	$cancellaCausale->start();
}

?>