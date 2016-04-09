<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/strumenti:/var/www/html/chopin/src/utility');
require_once 'cambiaContoStep2.class.php';

session_start();

$cambiaContoStep2 = CambiaContoStep2::getInstance();

if ($_GET["modo"] == "start") $cambiaContoStep2->start();
if ($_GET["modo"] == "go") {
	
	$cambiaContoStep2->go();
}
?>