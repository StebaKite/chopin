<?php

require_once 'modificaCorrispettivoNegozio.class.php';
require_once 'primanota.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(ModificaCorrispettivoNegozio::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();