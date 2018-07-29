<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'cancellaNuovoDettaglioFattura.class.php';
require_once 'fatture.controller.class.php';

session_start();

$_SESSION["Obj_fatturecontroller"] = serialize(new FattureController(CancellaNuovoDettaglioFattura::getInstance()));

$controller = unserialize($_SESSION["Obj_fatturecontroller"]);
$controller->start();
?>