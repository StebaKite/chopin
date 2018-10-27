<?php

require_once 'controlliApertura.class.php';
require_once 'main.controller.class.php';

session_start();

$_SESSION["Obj_maincontroller"] = serialize(new MainController(ControlliApertura::getInstance()));

$controller = unserialize($_SESSION["Obj_maincontroller"]);
$controller->start();