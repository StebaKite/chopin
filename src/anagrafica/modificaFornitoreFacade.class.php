<?php

require_once 'modificaFornitore.class.php';
require_once 'anagrafica.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(ModificaFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();

?>