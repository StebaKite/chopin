<?php

require_once 'riepiloghi.controller.class.php';
require_once 'riepilogoNegozi.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_riepiloghicontroller"] = serialize(new RiepiloghiController(RiepilogoNegozi::getInstance()));

$controller = unserialize($_SESSION["Obj_riepiloghicontroller"]);
$controller->start();