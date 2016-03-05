<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'estraiPdfRiepilogoNegozio.class.php';

session_start();

$estraiPdfRiepilogoNegozio = EstraiPdfRiepilogoNegozio::getInstance();

if ($_GET["modo"] == "start") {

	$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
	$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
	$_SESSION["saldiInclusi"] = $_REQUEST["saldiInclusi"];
	$_SESSION["soloContoEconomico"] = $_REQUEST["soloContoEconomico"];
	
	$estraiPdfRiepilogoNegozio->start();
}

?>