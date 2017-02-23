<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/riepiloghi:/var/www/html/chopin/src/utility');
require_once 'andamentoNegozi.controller.class.php';
require_once 'andamentoNegozi.class.php';

session_start();

if (!isset($_SESSION["Obj_andamentoNegozi"])) $_SESSION["Obj_andamentoNegozi"] = AndamentoNegozi::getInstance();
$controller = new AndamentoNegoziController($_SESSION["Obj_andamentoNegozi"]);

$controller->start();

?>