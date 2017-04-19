<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'escludiContoCausale.class.php';
require_once 'configurazioni.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(EscludiContoCausale::getInstance()));

$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
$controller->start();

?>