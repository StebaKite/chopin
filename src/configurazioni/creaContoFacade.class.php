<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaConto.class.php';
require_once 'configurazioni.controller.class.php';

session_start();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(CreaConto::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();


	$_SESSION["codconto"] = $_REQUEST["codconto"];
	$_SESSION["desconto"] = $_REQUEST["desconto"];
	$_SESSION["catconto"] = $_REQUEST["categoria"];
	$_SESSION["tipconto"] = $_REQUEST["dareavere"];
	$_SESSION["indpresenza"] = $_REQUEST["indpresenza"];
	$_SESSION["indvissottoconti"] = $_REQUEST["indvissottoconti"];
	$_SESSION["numrigabilancio"] = $_REQUEST["numrigabilancio"];		
	
	$_SESSION["sottocontiInseriti"] = $_POST["sottocontiInseriti"];
	$_SESSION["indexSottocontiInseriti"] = $_POST["indexSottocontiInseriti"];


?>