<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/utility');
require_once 'menubanner.class.php';

session_start();

$menubanner = Menubanner::getInstance();

if ($_GET["modo"] == "start") $menubanner->start();
if ($_GET["modo"] == "go") $menubanner->go();

?>
