<?php

require_once 'ricercaMercato.class.php';
require_once 'anagrafica.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaMercato::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();

?>