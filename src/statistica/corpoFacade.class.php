<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/utility');
require_once 'corpo.class.php';

session_start();

$corpo = Corpo::getInstance();

if ($_GET["modo"] == "start") {
	
	if (!isset($_SESSION["statoeventi"])) {
		$_SESSION["statoeventi"] = "00";
	}
	else {
		$_SESSION["statoeventi"] = $_REQUEST["statoeventi"];
	}
	
	$corpo->start();
}

?>