<?php

require_once 'aggiornaImportoScadenzaFornitore.class.php';
require_once 'primanota.controller.class.php';

session_start();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(AggiornaImportoScadenzaFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();