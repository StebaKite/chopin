<?php

require_once 'primanota.controller.class.php';
require_once 'ricercaScadenzeAperteFornitore.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaScadenzeAperteFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();