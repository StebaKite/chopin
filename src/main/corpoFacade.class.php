<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/utility');
require_once 'corpo.class.php';

session_start();

$corpo = Corpo::getInstance();

if ($_GET["modo"] == "start") $corpo->start();
if ($_GET["modo"] == "go") $corpo->go();

?>