<?php

require_once 'riepiloghi.controller.class.php';
require_once 'generaQuadroPresenzeAssistiti.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_riepiloghicontroller"] = serialize(new RiepiloghiController(GeneraQuadroPresenzeAssistiti::getInstance()));

$controller = unserialize($_SESSION["Obj_riepiloghicontroller"]);
$controller->start();