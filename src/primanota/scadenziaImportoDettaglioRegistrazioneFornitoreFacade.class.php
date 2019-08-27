<?php

require_once 'scadenziaImportoDettaglioRegistrazioneFornitore.class.php';
require_once 'primanota.controller.class.php';

session_start();

$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(ScadenziaImportoDettaglioRegistrazioneFornitore::getInstance()));

$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
$controller->start();