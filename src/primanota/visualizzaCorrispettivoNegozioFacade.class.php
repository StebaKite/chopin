<?php

require_once 'visualizzaCorrispettivoNegozio.class.php';
require_once 'primanota.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(VisualizzaCorrispettivoNegozio::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();