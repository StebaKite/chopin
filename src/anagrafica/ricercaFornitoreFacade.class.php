<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/anagrafica:/var/www/html/chopin/src/utility');
require_once 'ricercaFornitore.class.php';

session_start();

$ricercaFornitore = RicercaFornitore::getInstance();

if ($_GET["modo"] == "start") $ricercaFornitore->start();

?>