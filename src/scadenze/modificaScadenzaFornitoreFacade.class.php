<?php

require_once 'modificaScadenzaFornitore.class.php';
require_once 'scadenze.controller.class.php';

session_start();
xdebug_disable();

$_SESSION["Obj_scadenzecontroller"] = serialize(new ScadenzeController(ModificaScadenzaFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_scadenzecontroller"]);
$controller->start();