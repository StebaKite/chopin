<?php

require_once 'riportoSaldoPeriodico.class.php';
require_once 'saldi.controller.class.php';

session_start();

$_SESSION["Obj_saldicontroller"] = serialize(new SaldiController(RiportoSaldoPeriodico::getInstance()));

$controller = unserialize($_SESSION["Obj_saldicontroller"]);
$controller->start();