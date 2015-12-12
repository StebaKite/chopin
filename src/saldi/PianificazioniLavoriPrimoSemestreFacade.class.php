<?php

set_include_path('/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility');
require_once 'pianificazioniLavoriPrimoSemestre.class.php';

session_start();

$pianificazioniLavoriPrimoSemestre = PianificazioniLavoriPrimoSemestre::getInstance();

if ($_GET["modo"] == "start") $pianificazioniLavoriPrimoSemestre->start();

?>
