<?php

require_once 'creaCausale.class.php';
require_once 'configurazioni.controller.class.php';

session_start();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(CreaCausale::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();

?>