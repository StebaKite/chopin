<?php

require_once 'main.controller.class.php';
require_once 'main.class.php';

session_start();

if (!isset($_SESSION["Obj_maincontroller"])) {
    $_SESSION["Obj_maincontroller"] = serialize(new MainController(Main::getInstance()));
}

$controller = unserialize($_SESSION["Obj_maincontroller"]);
$controller->start();
