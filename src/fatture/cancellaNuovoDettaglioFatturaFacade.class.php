<?php

require_once 'cancellaNuovoDettaglioFattura.class.php';
require_once 'fatture.controller.class.php';

session_start();

$_SESSION["Obj_fatturecontroller"] = serialize(new FattureController(CancellaNuovoDettaglioFattura::getInstance()));

$controller = unserialize($_SESSION["Obj_fatturecontroller"]);
$controller->start();