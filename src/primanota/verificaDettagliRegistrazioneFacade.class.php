<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'verificaDettagliRegistrazione.class.php';
require_once 'primanota.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(VerificaDettagliRegistrazione::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();

?>