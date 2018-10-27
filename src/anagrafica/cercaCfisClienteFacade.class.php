<?php

require_once 'cercaCfisCliente.class.php';
require_once 'anagrafica.controller.class.php';

session_start();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(CercaCfisCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();

?>