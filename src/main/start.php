<?php

require_once 'main.controller.class.php';
require_once 'main.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_maincontroller"] = serialize(new MainController(Main::getInstance()));

$controller = unserialize($_SESSION["Obj_maincontroller"]);
$controller->start();
