<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/fatture:/var/www/html/chopin/src/utility');
require_once 'fatture.controller.class.php';
require_once 'creaFatturaCliente.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_fatturecontroller"] = serialize(new FattureController(CreaFatturaCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_fatturecontroller"]);
$controller->start();
?>