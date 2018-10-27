<?php

require_once 'cercaPivaCliente.class.php';
require_once 'anagrafica.controller.class.php';

session_start();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(CercaPivaCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();

?>