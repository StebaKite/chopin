<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'trends.class.php';

session_start();

$trends = Trends::getInstance();

if ($_GET["modo"] == "start") {

	require_once 'utility.class.php';

	$utility = Utility::getInstance();
	$array = $utility->getConfig();

	if ($array['primoSaldoDisponibile'] != "") { $_SESSION["datareg_da"] = $array['primoSaldoDisponibile']; }
	else { $_SESSION["datareg_da"] = "01/01/" . $_REQUEST["anno_eserczio_sel"]; }

	if ($array['ultimoSaldoDisponibile'] != "") { $_SESSION["datareg_a"] = $array['ultimoSaldoDisponibile']; }
	else { $_SESSION["datareg_a"] = "31/12/" . $_REQUEST["anno_eserczio_sel"]; }

	$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];

	$trends->start();
}

?>