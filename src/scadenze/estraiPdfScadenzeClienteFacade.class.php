<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/scadenze:/var/www/html/chopin/src/utility');
require_once 'estraiPdfScadenzeCliente.class.php';
require_once 'scadenze.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_scadenzecontroller"] = serialize(new ScadenzeController(EstraiPdfScadenzeCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_scadenzecontroller"]);
$controller->start();

?>