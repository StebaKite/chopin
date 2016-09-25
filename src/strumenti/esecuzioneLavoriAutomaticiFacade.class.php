#!/usr/bin/php
<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'esecuzioneLavoriAutomatici.class.php';

$esecuzioneLavoriAutomatici = new EsecuzioneLavoriAutomatici();
$esecuzioneLavoriAutomatici->start();

?>