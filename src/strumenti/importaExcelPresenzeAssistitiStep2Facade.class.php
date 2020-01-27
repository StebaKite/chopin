<?php

require_once 'importaExcelPresenzeAssistitiStep2.class.php';
require_once 'strumenti.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_strumenticontroller"] = serialize(new StrumentiController(ImportaExcelPresenzeAssistitiStep2::getInstance()));

$controller = unserialize($_SESSION["Obj_strumenticontroller"]);
$controller->start();