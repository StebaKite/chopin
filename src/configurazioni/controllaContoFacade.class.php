<?php

require_once 'controllaConto.class.php';
require_once 'configurazioni.controller.class.php';

session_start();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(ControllaConto::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();

?>