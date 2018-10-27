<?php

require_once 'inserisciSottoconto.class.php';
require_once 'configurazioni.controller.class.php';

session_start();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(InserisciSottoconto::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();

?>