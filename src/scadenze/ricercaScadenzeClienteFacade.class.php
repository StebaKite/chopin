<?php

require_once 'ricercaScadenzeCliente.class.php';
require_once 'scadenze.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_scadenzecontroller"] = serialize(new ScadenzeController(RicercaScadenzeCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_scadenzecontroller"]);
$controller->start();