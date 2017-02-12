<?php

interface RiepiloghiPresentation {
	
	public static function getInstance();
	
	public function inizializzaPagina();
	public function controlliLogici();	
	public function displayPagina();
	public function tabellaTotali($tipoTotale);
}

?>