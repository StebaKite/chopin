<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/primanota:/var/www/html/chopin/src/utility');
require_once 'creaRegistrazione.class.php';

$creaRegistrazione = new creaRegistrazione();

if ($_GET["modo"] == "start") $creaRegistrazione->start();
if ($_GET["modo"] == "go") $creaRegistrazione->go();

?>