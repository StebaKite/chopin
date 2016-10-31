<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'modificaRegistrazione.class.php';

session_start();

$modificaRegistrazione = ModificaRegistrazione::getInstance();

if ($_GET["modo"] == "start") {
	
	$_SESSION["idRegistrazione"] = $_REQUEST["idRegistrazione"];

	unset($_SESSION["esitoControlloDescrizione"]);
	unset($_SESSION["esitoControlloCausale"]);
	unset($_SESSION["esitoControlloFornitore"]);
	unset($_SESSION["esitoControlloCliente"]);
	unset($_SESSION["esitoControlloNumfatt"]);
	unset($_SESSION["esitoControlloDatascad"]);
	
	$modificaRegistrazione->start();
}

if ($_GET["modo"] == "go") {

	$_SESSION["idRegistrazione"] = $_POST["idRegistrazione"];
	$_SESSION["descreg"] = $_REQUEST["descreg"];
	$_SESSION["datascad"] = $_REQUEST["datascad"];
	$_SESSION["datareg"] = $_REQUEST["datareg"];
	$_SESSION["numfatt"] = $_REQUEST["numfatt"];
	$_SESSION["numfattCurrent"] = $_REQUEST["numfattCurrent"];
	$_SESSION["codneg"] = $_REQUEST["codneg"];
	$_SESSION["causale"] = $_REQUEST["causale"];
	$_SESSION["fornitore"] = $_REQUEST["fornitore"];
	$_SESSION["desforn"] = $_REQUEST["fornitore"];	
	$_SESSION["cliente"] = $_REQUEST["cliente"];
	$_SESSION["descli"] = $_REQUEST["cliente"];
	
	$_SESSION["esitoDescrizione"] = $_REQUEST["esitoDescrizione"];
	$_SESSION["esitoNumeroFattura"] = $_REQUEST["esitoNumeroFattura"];
	$_SESSION["esitoCausale"] = $_REQUEST["esitoCausale"];
	$_SESSION["esitoFornitore"] = $_REQUEST["esitoFornitore"];
	$_SESSION["esitoCliente"] = $_REQUEST["esitoCliente"];
	$_SESSION["esitoNumfatt"] = $_REQUEST["esitoNumfatt"];
	$_SESSION["esitoDatascad"] = $_REQUEST["esitoDatascad"];
	
	$_SESSION["esitoControlloDataRegistrazione"] = $_REQUEST["esitoControlloDataRegistrazione"];

	$modificaRegistrazione->go();
}

?>