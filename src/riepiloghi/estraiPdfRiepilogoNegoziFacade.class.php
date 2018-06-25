<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'estraiPdfRiepilogoNegozio.class.php';
require_once 'riepiloghi.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_riepiloghicontroller"] = serialize(new RiepiloghiController(EstraiPdfRiepilogoNegozio::getInstance()));

$controller = unserialize($_SESSION["Obj_riepiloghicontroller"]);
$controller->start();
?>