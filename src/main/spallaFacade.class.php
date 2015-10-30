<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/utility');
require_once 'spalla.class.php';

session_start();

$spalla = Spalla::getInstance();

if ($_GET["modo"] == "start") $spalla->start();
if ($_GET["modo"] == "go") $spalla->go();

?>
