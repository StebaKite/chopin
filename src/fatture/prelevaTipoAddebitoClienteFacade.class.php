<?php

require_once 'fatture.controller.class.php';
require_once 'prelevaTipoAddebitoCliente.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_fatturecontroller"] = serialize(new FattureController(PrelevaTipoAddebitoCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_fatturecontroller"]);
$controller->start();