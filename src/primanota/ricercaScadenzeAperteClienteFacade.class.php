<?php

require_once 'primanota.controller.class.php';
require_once 'ricercaScadenzeAperteCliente.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaScadenzeAperteCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();