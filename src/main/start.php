<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/utility');
require_once 'main.class.php';

session_start();

$main = Main::getInstance();
$main->start();

?>