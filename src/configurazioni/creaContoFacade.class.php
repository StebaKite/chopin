<?php

require_once 'creaConto.class.php';
require_once 'configurazioni.controller.class.php';

session_start();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(CreaConto::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();

?>