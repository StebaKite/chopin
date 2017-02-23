<?php

class MainController {
	
	private $main;
	
	public function __construct(MainBusinessInterface $main) {
		$this->main = $main;
	}
	
	public function start() {		
		$this->main->start();
	}
}

?>