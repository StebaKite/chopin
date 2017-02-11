<?php

interface Riepiloghi {
	
	public static function getInstance();
	
	public function start();
	public function go();
	public function ricercaDati($utility);
	public function preparaPagina($template);
	
	
	
}