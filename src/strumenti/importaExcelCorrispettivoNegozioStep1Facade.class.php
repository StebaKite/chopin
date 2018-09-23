<?php

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/configurazioni:/var/www/html/chopin/src/utility');
require_once 'importaExcelCorrispettivoNegozioStep1.class.php';
require_once 'strumenti.controller.class.php';

session_start();

$_SESSION["Obj_strumenticontroller"] = serialize(new StrumentiController(ImportaExcelCorrispettivoNegozioStep1::getInstance()));

$controller = unserialize($_SESSION["Obj_strumenticontroller"]);
$controller->start();
?>