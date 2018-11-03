<?php

require_once 'cambiaContoStep2.class.php';
require_once 'strumenti.controller.class.php';

session_start();

$_SESSION["Obj_strumenticontroller"] = serialize(new StrumentiController(CambiaContoStep2::getInstance()));

$controller = unserialize($_SESSION["Obj_strumenticontroller"]);
$controller->start();