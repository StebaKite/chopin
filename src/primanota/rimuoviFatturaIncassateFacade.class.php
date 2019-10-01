<?php

require_once 'rimuoviFatturaIncassata.class.php';
require_once 'primanota.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RimuoviFatturaIncassata::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();