<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/strumenti:/var/www/html/chopin/src/utility');
require_once 'lavoriAutomatici.class.php';

session_start();
xdebug_disable();

$lavoriAutomatici = LavoriAutomatici::getInstance();

if ($_REQUEST["modo"] == "start") $lavoriAutomatici->start();

?>