<?php

require_once 'annullaNuovoCorrispettivoNegozio.class.php';
require_once 'primanota.controller.class.php';

session_start();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(AnnullaNuovoCorrispettivoNegozio::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();