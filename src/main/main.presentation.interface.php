<?php

require_once 'nexus6.main.interface.php';

interface MainPresentationInterface extends MainNexus6Interface {
	
	const MAIN_PAGE = "/main/main.html";
	
	public function controlliLogici();
	public function displayPagina();
}

?>