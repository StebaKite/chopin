<?php

require_once 'aggiungiNuovoDettaglioFattura.class.php';
require_once 'fatture.controller.class.php';

session_start();

$_SESSION["Obj_fatturecontroller"] = serialize(new FattureController(AggiungiNuovoDettaglioFattura::getInstance()));

$controller = unserialize($_SESSION["Obj_fatturecontroller"]);
$controller->start();