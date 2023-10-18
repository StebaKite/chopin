#!/usr/bin/php
<?php
/* * ***************************************************************************************************
 *
 * Questa script esegue tutti i lavori automatici che trova da eseguire antecedenti la data odierna.
 * Deve essere inserita in Crontab ed eseguita al 45esimo minuto di ogni ora
 *
 * Dopo l'esecuzione di questa script può essere eseguita la script di backup.
 *
 * @author stefano
 *
 */

set_include_path('/var/www/html/chopin/src/_core_:/var/www/html/chopin/src/main:/var/www/html/chopin/src/saldi:/var/www/html/chopin/src/utility:/var/www/html/chopin/src/strumenti');
require_once 'esecuzioneLavoriAutomatici.class.php';

$esecuzioneLavoriAutomatici = new EsecuzioneLavoriAutomatici();
$esecuzioneLavoriAutomatici->start();
?>