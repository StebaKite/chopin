<?php

require_once 'importaExcelPresenzeAssistitiStep1.class.php';
require_once 'strumenti.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_strumenticontroller"] = serialize(new StrumentiController(ImportaExcelPresenzeAssistitiStep1::getInstance()));

$controller = unserialize($_SESSION["Obj_strumenticontroller"]);
$controller->start();