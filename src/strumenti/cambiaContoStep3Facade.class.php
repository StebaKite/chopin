<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/strumenti:/var/www/html/chopin/src/utility');
require_once 'cambiaContoStep3.class.php';

session_start();

$cambiaContoStep3 = CambiaContoStep3::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["conto_sel_nuovo"] = $_REQUEST["conto_sel_nuovo"];
	$cambiaContoStep3->start();
}
if ($_GET["modo"] == "go")	  $cambiaContoStep3->go();

?>