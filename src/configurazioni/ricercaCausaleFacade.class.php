<?php

require_once 'ricercaCausale.class.php';
require_once 'configurazioni.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();

?>