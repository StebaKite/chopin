<?php

require_once 'modificaConto.class.php';
require_once 'configurazioni.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(ModificaConto::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();