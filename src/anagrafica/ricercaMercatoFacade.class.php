<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'ricercaMercato.class.php';

session_start();

$ricercaMercato = RicercaMercato::getInstance();

if ($_GET["modo"] == "start") $ricercaMercato->start();

?>