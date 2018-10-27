<?php

require_once 'creaFornitore.class.php';
require_once 'anagrafica.controller.class.php';

session_start();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(CreaFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();

?>