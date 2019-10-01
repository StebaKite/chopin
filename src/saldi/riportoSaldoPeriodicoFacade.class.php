<?php

require_once 'riportoSaldoPeriodico.class.php';
require_once 'saldi.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_saldicontroller"] = serialize(new SaldiController(RiportoSaldoPeriodico::getInstance()));

$controller = unserialize($_SESSION["Obj_saldicontroller"]);
$controller->start();