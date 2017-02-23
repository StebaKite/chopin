<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'creaCliente.class.php';
require_once 'anagrafica.controller.class.php';

session_start();

$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(CreaCliente::getInstance()));

$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
$controller->start();

?>