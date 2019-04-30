<?php

require_once 'cancellaDettaglioRegistrazione.class.php';

session_start();

$cancellaDettaglioRegistrazione = CancellaDettaglioRegistrazione::getInstance();

if ($_GET["modo"] == "go") {

	$_SESSION["idDettaglioRegistrazione"] = $_POST["idDettaglioRegistrazione"];	
	$cancellaDettaglioRegistrazione->go();
}

?>