<?php

require_once 'modificaCliente.class.php';
require_once 'anagrafica.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(ModificaCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();