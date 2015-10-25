<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'riportoSaldoPeriodico.class.php';

session_start();

$riportoSaldoPeriodico = RiportoSaldoPeriodico::getInstance();

if ($_GET["modo"] == "start") $riportoSaldoPeriodico->start();
if ($_GET["modo"] == "go") $riportoSaldoPeriodico->go();

?>
