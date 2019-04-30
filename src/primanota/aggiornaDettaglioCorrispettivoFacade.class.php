<?php

require_once 'aggiornaDettaglioCorrispettivo.class.php';
require_once 'primanota.controller.class.php';

session_start();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(AggiornaDettaglioCorrispettivo::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();

?>