<?php

require_once 'ricercaScadenzeFornitore.class.php';
require_once 'scadenze.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_scadenzecontroller"] = serialize(new ScadenzeController(RicercaScadenzeFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_scadenzecontroller"]);
$controller->start();